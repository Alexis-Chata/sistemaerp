$(document).ready(function () {

    $('#msjNregistro').hide();
    $('#imprimir').hide();
    $('#blockEnProceso').hide();
    contenedorCI = $('#contenedorCI');
    contenedorCI.hide();
    contenedorAnadir = $('#contenedorAnadir');
    contenedorAnadir.hide();
        
    $('#contenedorAnadir').dialog({
        title: 'Añadir Registro',
        autoOpen: false,
        modal: true,
        resizable: true,
        width: 650,
        buttons: {
            "Confirmar": function () {
                if ($('#txtFechaInicio').val().length != 10) {
                    $('#txtFechaInicio').val('');
                    $('#txtFechaInicio').focus();
                } else if ($('#idHoraInicio').val() <= 7 || $('#idHoraInicio').val() > 21) {
                    $('#idHoraInicio').val('');
                    $('#idHoraInicio').focus();
                } else if ($('#idMinutoInicio').val() < 0 || $('#idMinutoInicio').val() > 59) {
                    $('#idMinutoInicio').val('');
                    $('#idMinutoInicio').focus();
                } else if ($('#txtFechafin').val().length != 10) {
                    $('#txtFechafin').val('');
                    $('#txtFechafin').focus();
                } else if ($('#idHoraFin').val() <= 7 || $('#idHoraFin').val() > 21) {
                    $('#idHoraFin').val('');
                    $('#idHoraFin').focus();
                } else if ($('#idMinutoFin').val() < 0 || $('#idMinutoFin').val() > 59) {
                    $('#idMinutoFin').val('');
                    $('#idMinutoFin').focus();
                } else if ($('#idCantidad').val() <= 0 || $('#idCantidad').val() > $('#idCantidad').data('cantidad')) {
                    $('#idCantidad').focus();
                } else if ($('#idPassword').val().length == 0) {
                    $('#idPassword').focus();
                } else if ($('#InfTecnico').val().length == 0) {
                    $('#InfTecnico').focus();
                } else {
                    $.ajax({
                        url: '/serviciotecnico/grabacontrolinterno',
                        type: 'post',
                        dataType:'html',
                        data: new FormData($("#frmNuevoRegistro")[0]),
                        contentType: false,
                        processData: false,
                        success: function (resp) {                            
                            if (resp == 1) {
                                limpiar_anadirregistro();
                                $('#contenedorAnadir').dialog('close');
                                listado_controlinternost($('#txtiddetallerecepciontecnico').val());
                                listado_enproceso($('#idTecnico').val());
                                $('.bitacora[data-id="' + $('#idTecnico').val() + '"]').parents('tr').addClass('active-row');
                            } else {
                                $('#msjNregistro').show();
                                $('#msjNregistro').html(resp);
                            }                            
                        }
                    });
                }
            }
        }, close: function () {
            limpiar_anadirregistro();
        }
    });
    
    $('#contenedorCI').dialog({
        title: 'Bitacora de Reparaciones ',
        autoOpen: false,
        modal: true,
        resizable: true,
        width: 900,
        buttons: {
            "Añadir Registro": function () {
                if ($('#DiagtblProceso').data('garantia') == 1) {
                    $("input[name=rdGarantia][value='0']").attr('checked', false);
                    $("input[name=rdGarantia][value='1']").attr('checked', true);
                    $('#blockGarantia').show();
                } else {
                    $("input[name=rdGarantia][value='0']").attr('checked', true);
                    $("input[name=rdGarantia][value='1']").attr('checked', false);
                    $('#blockGarantia').hide();
                }
                $('#contenedorAnadir').dialog('open');
            }
        }, close: function () {
            $('#tblEnProceso tr').removeClass();
            limpiar_anadirregistro();
        }
    });
    
    $('#txtFechaInicio').change(function () {
        if ($(this).val().length == 10) {
            $('#idHoraInicio').focus();
        }
    });
    
    $('#idHoraInicio').keyup(function () {
        if ($(this).val() > 1) {
            $('#idMinutoInicio').focus();
        }
    });

    $('#idHoraInicio').focusout(function () {
        if ($(this).val().length == 1) {
            $(this).val('0' + $(this).val());
        }
    });

    $('#idMinutoInicio').keyup(function () {
        if ($(this).val() >= 6 && $(this).val() <= 59) {
            $('#txtFechafin').focus();
        }
    });

    $('#idMinutoInicio').focusout(function () {
        if ($(this).val().length == 1) {
            $(this).val('0' + $(this).val());
        }
    });
   
    $('#txtFechafin').change(function () {
        if ($(this).val().length == 10) {
            $('#idHoraFin').focus();
        }
    });
    
    $('#idHoraFin').keyup(function () {
        if ($(this).val() > 1) {
            $('#idMinutoFin').focus();
        }
    });

    $('#idMinutoFin').focusout(function () {
        if ($(this).val().length == 1) {
            $(this).val('0' + $(this).val());
        }
    });

    $('#idMinutoFin').keyup(function () {
        if ($(this).val() >= 6 && $(this).val() <= 59) {
            $('#opcSituacion').focus();
        }
    });

    $('#idMinutoFin').focusout(function () {
        if ($(this).val().length == 1) {
            $(this).val('0' + $(this).val());
        }
    });
    
    $('#idCantidad').keyup(function () {
        if ($(this).val() > 0 && $(this).val() <= $(this).data('cantidad')) {
            $(this).removeClass('color-rojo');
        } else {
            $(this).addClass('color-rojo');
        }
    });
    
    $('#idCantidad').focusout(function () {
        if ($(this).val() > 0 && $(this).val() <= $(this).data('cantidad')) {
            $(this).removeClass('color-rojo');
        } else {
            $(this).addClass('color-rojo');
            $(this).focus();
        }
    });
    
    $('#txtIdTecnico').autocomplete({
        source: "/serviciotecnico/buscaactucompletetecnico/",
        select: function (event, ui) {
            $('#idTecnico').val(ui.item.id);
            $('#CabeceraTecnico').html(ui.item.label);
            listado_enproceso(ui.item.id);
            $('#imprimir').show();
            $('#blockEnProceso').show();            
        }
    });

    $('#imprimir').click(function (e) {
        e.preventDefault();
        $('#imprimir').hide();
        $('table tr td, table tr th').css('font-family', 'courier');
        $('body').css('color', 'black');
        imprSelec('tblEnProceso');
        $('#imprimir').show();
        $('table tr td, table tr th').css('font-family', 'Calibri');
    });
    
    $('#tblEnProceso').on('click', '.bitacora', function () {
        $('#tblEnProceso tr').removeClass();
        $(this).parents('tr').addClass('active-row');
        listado_controlinternost($(this).data('id'));   
    });

});

function listado_controlinternost(id) {
    $.ajax({
        url: '/serviciotecnico/listadocontrolinternost',
        type: 'post',
        dataType:'json',
        data: {'iddetallerecepciontecnico': id},
        success: function (resp) {
            $('#divCI').html(resp['divCI']);
            $('#txtiddetallerecepciontecnico').val(id);
            $('#DiagtblProceso tbody').html(resp['tblProceso']);
            $('#DiagtblProceso tfoot').html(resp['tblProcesoFoot']);
            $('#DiagtblProceso').data('garantia', resp['garantia'])
            $('#msjCantidad').html('Max. ' + resp['Max']);
            $('#idCantidad').data('cantidad', resp['Max']);
            $('#contenedorCI').dialog('open');
        }
    }); 
}

function limpiar_anadirregistro() {
    $('#txtFechaInicio').val('');
    $('#idHoraInicio').val('');
    $('#idMinutoInicio').val('');
    $('#txtFechafin').val('');
    $('#idHoraFin').val('');
    $('#idMinutoFin').val('');
    $('#opcSituacion').val(1);
    $('#idCantidad').val('');
    $('#idPassword').val('');
    $('#InfTecnico').val('');
    $('#idGastoSoles').val('');
    $('#idGastoDolares').val('');
    $('#idGastoDolares').val('');
    $('#idImgs').val('');
    $('#msjNregistro').hide();
}

function listado_enproceso(idtecnico) {
    $.ajax({
        url: '/serviciotecnico/listadoenproceso_tecnico',
        type: 'post',
        datatype: 'html',
        data: {'idtecnico': idtecnico},
        success: function (resp) {
            $('#tblEnProceso tbody').html(resp);
        }
    });
}