$(document).ready(function () {
   
    $('.pagAtendido').click(function () {
        $('#frmBusqueda').attr('action', '/serviciotecnico/atendidos/' + $(this).data('pag'));
        $('#frmBusqueda').submit();
    });
    
    $('#btncerrar').click(function (e) {
        $('#tblPendientes tr').removeClass();
        $('#detalle').hide('Blind');
    });
    
    $('.verdetalle').click(function (e) {
        $('#tblPendientes tr').removeClass();
        $(this).parents('tr').addClass('active-row');
        $('#detalle').hide('Blind');
        var iddetallerecepcion = $(this).data('iddetallerecepcion');
        e.preventDefault();
        $.ajax({
            url: '/serviciotecnico/vernotificacionatendido/',
            type: 'post',
            dataType: 'json',
            data: {'iddetallerecepcion': iddetallerecepcion},
            success: function (resp) {
                $('#mensaje').html(resp['codigost']);
                $('#tblcabecera thead').html(resp['cabecera']);
                $('#tblcabecera tbody').html(resp['cuerpo']);                
                $('#tblDetalleAtendido thead').html(resp['cabeceraatendido']);
                $('#tblDetalleAtendido tbody').html(resp['detalleatendido']);
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