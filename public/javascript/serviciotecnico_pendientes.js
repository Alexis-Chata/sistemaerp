$(document).ready(function () {

    contenedorAtender = $('#contenedorAtender');
    contenedorAtender.hide();
    $('#eliminarTecnico').hide();

    $('#contenedorAtender').dialog({
        title: 'Preparar Reparación',
        autoOpen: false,
        modal: true,
        resizable: true,
        width: 500,
        buttons: {
            "Confirmar": function () {
                if ($('#txtidPersonal').val().length == 0) {
                    limpiarCampos();
                } else if ($('#idPassword').val().length == 0) {
                    $('#idPassword').focus();
                } else if ($('#txtFechaInicio').val().length != 10) {
                    $('#txtFechaInicio').val();
                    $('#txtFechaInicio').focus();
                } else if ($('#idCantidadReparar').val() <= 0 || $('#idCantidadReparar').val() > $('#idCantidadReparar').data('cantidad')) {
                    $('#idCantidadReparar').focus();
                } else {
                    $('#frmAtender').submit();
                }
            }
        }, close: function () {
            $('#idCantidadReparar').data('cantidad', 0);
            $('#txtIdDetalleRecepcion').val('');
            $('#tblPendientes tr').removeClass();
        }
    });
    
    $('#idPersonal').autocomplete({
        source: "/serviciotecnico/buscaactucompletetecnico/",
        select: function (event, ui) {
            $('#idPersonal').attr('disabled', 'disabled');
            $('#txtidPersonal').val(ui.item.id);
            $('#eliminarTecnico').show();
            $('#idPassword').focus();
        }
    });
    
    $('#eliminarTecnico').click(function () {
        limpiarCampos();
    });
    
    $('#txtFechaInicio').change(function () {
        if ($(this).val().length == 10) {
            $('#idCantidadReparar').focus();
        }
    });
    
    $('#tbldetalles').on('click', '.DetalleReparado', function () {        
        var idddetallerecepcion = $(this).data('iddetallerecepcion');
        if ($(this).data('abierto') == 0) {
            $(this).data('abierto', '1');            
            $.ajax({
                url:'/serviciotecnico/verdetallenotificacion',
                type:'post',
                datatype:'html',
                data:{'iddetallerecepcion':idddetallerecepcion},
                success:function(resp){
                      $('#DetalleAtendido' + idddetallerecepcion + ' tbody').html(resp);
                }
            });            
            $('#DetalleAtendido' + idddetallerecepcion).show('Blind');
            $(this).attr('src', '/imagenes/iconos/OrdenArriba.gif');
        } else {
            $(this).data('abierto', '0');
            $('#DetalleAtendido' + idddetallerecepcion).hide('Blind');
            $(this).attr('src', '/imagenes/iconos/OrdenAbajo.gif');
        }        
    });

    $('.btnAtender').click(function () {
        limpiarCampos();
        $('#tblPendientes tr').removeClass();
        $(this).parents('tr').addClass('active-row');
        $('#msjCantidad').html('Desde 1 hasta ' + $(this).data('cantidad'));
        $('#idCantidadReparar').data('cantidad', $(this).data('cantidad'));
        $('#txtIdDetalleRecepcion').val($(this).data('id'));
        $('#contenedorAtender').dialog('open');
    });

    $('#btncerrar').click(function (e) {
        $('#tblPendientes tr').removeClass();
        $('#detalle').hide('Blind');
    });

    $('.verdetalle').click(function (e) {
        $('#tblPendientes tr').removeClass();
        $(this).parents('tr').addClass('active-row');
        $('#detalle').hide('Blind');
        var idrecepcion = $(this).data('id');
        e.preventDefault();
        $.ajax({
            url: '/serviciotecnico/vernotificacion/',
            type: 'post',
            dataType: 'json',
            data: {'idrecepcion': idrecepcion, 'tipo': $('#textTipo').val()},
            success: function (resp) {
                $('#mensaje').html(resp['codigost']);
                $('#tblcabecera thead').html(resp['cabecera']);
                $('#tbldetalles tbody').html(resp['detalle']);
                $('#detalle').show('Blind');
                $('html,body').animate({
                    scrollTop: $("#detalle").offset().top
                }, 1500);
            }
        });
    });

    $('#imprimir').click(function (e) {
        e.preventDefault();
        $('#imprimir').hide();
        $('#btncerrar').hide();
        $('table tr td, table tr th').css('font-family', 'courier');
        $('body').css('color', 'black');
        imprSelec('detalle');
        $('#imprimir').show();
        $('#btncerrar').show();
        $('table tr td, table tr th').css('font-family', 'Calibri');
    });

});

function limpiarCampos() {
    $('#idPersonal').val('');
    $('#txtidPersonal').val('');
    $('#idPersonal').removeAttr('disabled');
    $('#idPassword').val('');
    $('#eliminarTecnico').hide();
    $('#idPersonal').focus();
}