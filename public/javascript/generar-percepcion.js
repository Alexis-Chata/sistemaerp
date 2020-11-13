$(document).ready(function () {
    
    actualizarNroPercepcion();

    $('#txtOrdenVenta').autocomplete({
        source: "/ordenventa/autocompletePercepcion/",
        select: function (event, ui) {
            $('#txtIdOrden').val(ui.item.id);
            buscaOrdenVenta();
            cargaDetalle();
            actualizarNroPercepcion();
        }
    });
    
    $('#txtSerie').change(function () {
        actualizarNroPercepcion();
    });

});

function actualizarNroPercepcion () {
    var ruta = "/facturacion/actualizarCorrelativos/";
    $.ajax({
            url:ruta,
            type:'post',
            data:{'idguia':0,'tipo':10, 'serie': $('#txtSerie').val()},
            success:function(datos){
                $('#blockCorrelativo').html(datos);
            }, error: function(a, b, c) {
                console.log(a);
                console.log(b);
                console.log(c);
            }
    });
}

function buscaOrdenVenta(){
    if ($('#txtIdOrden').val() != '') {
        var ordenVenta = $('#txtIdOrden').val();
	var ruta = "/ordenventa/buscarCliente/" + ordenVenta;
	$.getJSON(ruta, function(data){
            $('#txtCliente').val(data.razonsocial);
            $('#txtruc').val(data.ruc);
            $('#txtDireccion').val(data.direccion);
            $('#txtemision').val($('#fechadoc').val());
	});
    }
}

function cargaDetalle() {
    if ($('#txtIdOrden').val() != '') {
        var ordenVenta = $('#txtIdOrden').val();
	var ruta = "/ordenventa/cargarDetallePercepcion/" + ordenVenta;
	$.getJSON(ruta, function(data){
            $('#tblConDescripcion').html(data.contenido);
            $('#totalPerc').html(data.moneda + data.totalPercepcion);
            $('#txttotal').html(data.moneda + data.total);
            $('#txtPorAsignar').val(data.moneda + data.totalPercepcionAsignar);
            $('#divOrdenCobro').html(data.tblOrdenCobro);
	});       
    }
}
