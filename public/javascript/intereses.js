$(document).ready(function () {
    
    contenedorinteres = $('#contenedorintereses');
    
    $('#txtOrdenVenta2').autocomplete({
        source: "/ordenventa/PendientesxPagar2/",
        select: function (event, ui) {
            $('#txtIdOrden').val(ui.item.id);
            buscaOrdenCobro();
            cargaDetalleOrdenCobro2();
        }
    });
    
    $('#tblDetalleOrdenCobro').on('click', '.btnIntereses', function () {
        $('#txtDetalleordencobro').val($(this).data('id'));
        contenedorinteres.dialog('open');
    });
    
    contenedorinteres.dialog({
        autoOpen:false,
        modal:true,
        buttons:{
                Aceptar: function(){
                    $.ajax({
                        url:'/ventas/intereses',
                        type:'post',
                        datatype:'html',
                        data:{'iddetalleordencobro':$('#txtDetalleordencobro').val(),'intereses':$('#idOpcIntereses').val()},
                        success:function(resp){
                            buscaOrdenCobro();
                            cargaDetalleOrdenCobro2();
                            contenedorinteres.dialog('close');
                        }
                });
            }
        },
        close:function(){
                $('#txtDetalleordencobro').val('');
        }
});

});

function buscaOrdenCobro() {
    var ordenVenta = $('#txtIdOrden').val();
    var ruta = "/ordencobro/buscarxOrdenVenta/" + ordenVenta;
    $.getJSON(ruta, function (data) {
        $('#razonsocial').val(data.razonsocial);
        $('#idcliente').val(data.idcliente);
        $('#ruc').val(data.ruc);
        $('#codigo').val(data.codcliente);
        $('#codantiguo').val(data.codantiguo);
        $('.inline-block input').exactWidth();
    });
}

function cargaDetalleOrdenCobro2() {
    var ordenVenta = $('#txtIdOrden').val();
    var ruta = "/ordencobro/buscarDetalleOrdenCobroIntereses/" + ordenVenta;
    $.post(ruta, function (data) {
        $('#tblDetalleOrdenCobro tbody').html(data);
        //verificarDiferenciaTotales();
    });
}