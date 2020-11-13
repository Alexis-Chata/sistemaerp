$(document).ready(function () {

    $('#btnImprimir').click(function (e) {
        e.preventDefault();
        $('listados').append('<link rel="stylesheet" href="/css/normalize.css">');
        imprSelec('listados');

    });

    $('#txtproducto').attr('required', 'required');
    $('#btnCargaKardex').click(function (e) {
        var periodoInicial = ($('#periodoInicial').val() != '' ? $('#periodoInicial').val() : new Date().getFullYear());
        var periodoFinal = ($('#periodoFinal').val() != '' ? $('#periodoFinal').val() : new Date().getFullYear());
        var mesInicial = ($('#mesInicial').val() != '' ? $('#mesInicial').val() : (new Date().getMonth())+1);
        var mesFinal = ($('#mesFinal').val() != '' ? $('#mesFinal').val() : (new Date().getMonth())+1);
        if ($('#txtproducto').val() == "") {
            alert("Ingrese el Producto");

        }
        else if ((periodoInicial>periodoFinal) || ((periodoInicial==periodoFinal)&&mesInicial>mesFinal)) {
            alert("La fecha inicial debe ser menor o igual a la fecha final");
        }
        else {
            var idProducto = $('#idProducto').val();
            var sunat = 0;
            if ($('#sunat').attr('checked') == "checked") {
                sunat = 1;
            }

            var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

            var txtProductolabel = $('#txtDescripcion').val();
            $('#labelProducto').html(txtProductolabel);
            $('#labelCodigo').html($('#txtproducto').val());
            var final = '';
            if (mesInicial != mesFinal && periodoInicial != periodoFinal) {
                final = ' - ' + meses[(mesFinal-1)] + ' ' + periodoFinal;
            }
            $('#labelPeriodo').html(meses[(mesInicial-1)] + ' ' + periodoInicial + final);

            $('#labelalmacen').html('Almacén General');
            $('#labelMetodo').html('Promedio Movil');
            $('#labelTipo').html('Mercaderias');
            cargaKardexValorizadoxProductoFecha(idProducto, periodoInicial, periodoFinal, mesInicial, mesFinal, sunat);
        }
    });
});

function cargaKardexValorizadoxProductoFecha(idProducto, periodoInicial, periodoFinal, mesInicial, mesFinal, sunat) {
    var url = "/movimiento/kardexValorizadoxProducto/" + idProducto;
    $.post(url, {anoInicial: periodoInicial, anoFinal: periodoFinal,  mesInicial: mesInicial, mesFinal: mesFinal, sunat: sunat}, function (data) {

        $('#tblKardexValorizado tbody').html(data);
    });
}

