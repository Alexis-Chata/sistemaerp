$(document).ready(function () {

    contenedorremitir = $('#contenedorremitir');
    contenedorremitir.hide();

    $('.btnremitir').click(function () {
//        $('#tblRecepciones tr').removeClass();
//	$(this).parents('tr').addClass('active-row');
//        $('#idRecepRemision').val($(this).data('id'));
//        $('#contenedorremitir').dialog('open');
        $('#idRecepRemision').val($(this).data('id'));
        $('#frmRemitir').submit();
    });

    $('#contenedorremitir').dialog({
        title: 'Control Interno de Serv. Tecnico',
        autoOpen: false,
        modal: true,
        resizable: true,
        width: 350,
        buttons: {
            "Enviar": function () {
                if ($('#idfremision').val().length == 0) {
                    $('#idfremision').focus();
                } else {
                    $('#frmRemitir').submit();
                }
            }
        }, close:function(){
            $('#idRecepRemision').val('');
            $('#tblRecepciones tr').removeClass();
        }
    });

    $('.verdetalle').click(function (e) {
        $('#detalle').hide('Blind');
        var idrecepcion = $(this).data('id');
        e.preventDefault();
        $.ajax({
            url: '/atencioncliente/verrecojomercaderia/',
            type: 'post',
            dataType: 'json',
            data: {'idrecepcion': idrecepcion},
            success: function (resp) {
                $('#mensaje').html(' Recojo  N° ' + idrecepcion);
                $('#tblcabecera thead').html(resp['cabecera']);
                $('#tbldetalles tbody').html(resp['detalle']);
                $('#detalle').show('Blind');
                $('html,body').animate({
                    scrollTop: $("#detalle").offset().top
                }, 1500);
            }
        });
    });
    
    $('.verdetalleaprobado').click(function (e) {
        $('#detalle').hide('Blind');
        var idrecepcion = $(this).data('id');
        e.preventDefault();
        $.ajax({
            url: '/atencioncliente/verrecojomercaderiaaprobado/',
            type: 'post',
            dataType: 'json',
            data: {'idrecepcion': idrecepcion},
            success: function (resp) {
                $('#mensaje').html(' Recojo: ' + resp['codigost']);
                $('#tblcabecera thead').html(resp['cabecera']);
                $('#tbldetalles tbody').html(resp['detalle']);
                $('#detalle').show('Blind');
                $('html,body').animate({
                    scrollTop: $("#detalle").offset().top
                }, 1500);
            }
        });
    });
    
    $('#btncerrar').click(function (e) {
        $('#detalle').hide('Blind');
    });

    $(".recEliminar").click(function (e) {
        if (!confirm('¿Esta Seguro de Eliminar el Recojo de Mercaderia?')) {
            e.preventDefault();
        }
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