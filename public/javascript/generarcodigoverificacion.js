$(document).ready(function () {

    $('#txtOrdenVenta').focus();

    $('#txtOrdenVenta').autocomplete({
        source: "/ordenventa/buscarautocompletecompletopendientes/",
        select: function (event, ui) {
            $('#txtIdordenventa').val(ui.item.id);
            buscaOrdenCobro();
        }
    });
    
    $('#frmGenerarCodigo').submit(function () {
        if ($('#txtIdordenventa').val() == '') {
            $('#txtOrdenVenta').focus();
            $('#txtOrdenVenta').css('background', '#ffa8a8');
            return false;
        }

        if (!($('#nroCodigos').val() >= 1 && $('#nroCodigos').val() <= 4) ) {
            $('#nroCodigos').focus();
            $('#nroCodigos').css('background', '#ffa8a8');
            return false;
        }
        
    });

});

function buscaOrdenCobro(){
	var ordenVenta = $('#txtIdordenventa').val();
	var ruta = "/ordencobro/buscarxOrdenVenta/" + ordenVenta;
	$.getJSON(ruta, function(data){
		$('#razonsocial').val(data.razonsocial);
		$('#ruc').val(data.ruc);
		$('#codigov').val(data.codigov);
	});
}