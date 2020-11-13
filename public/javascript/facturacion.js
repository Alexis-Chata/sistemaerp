$(document).on('ready', function () {
    $('#txtBusqueda').autocomplete({
        source: "/facturacion/filtro/",
        select: function (event, ui) {
            var idOrdenVenta = ui.item.id;
            valoridordenventa = ui.item.id;
        }
    });
    $("#seleccion").change(function () {
        var id = $("#seleccion option:selected").text();
        var url = '/facturacion/listaOrdenVenta/' + id;
        window.location = url;
    });
    $(".imprimir").click(function (e) {
        var resp = confirm('Desea Realmente Imprimir');
        if (!resp) {
            e.preventDefault();
        }

    });
    $(".detalleOV").hide();

    $(".observar").on('click', function (e) {
        e.preventDefault();
        id = $(this).attr('id');
        //alert('Numero de Orden de Venta: '+id);
        ruta = "/ordenventa/detalle/" + id;
        $.ajax({
            url: '/ordenventa/detalle/',
            type: 'GET',
            data: {'IDOV': id},
            success: function (respuesta) {
                //console.log(respuesta);
                $(".detalleOV").hide('Blind');
                $("#tablita tbody").html(respuesta);

                $(".detalleOV").show('Blind');
            },
            error: function (error) {
                //console.log(error);
            }
        });
    });
    $("#cierraTabla").click(function (e) {
        $(".detalleOV").hide('Blind');

    });

    $(":checkbox").click(function () {
        idordenventa = $(this).attr('rel');
        campo = $(this).val();
        $.post('/facturacion/updateDespacho', {idordenventa: idordenventa, campo: campo}, function (data) {
            if (data == true) {
                alert("datos actualizados");

            }

        }, 'json');

    });
    $(".observacion").keyup(function () {
        valor = $(this).val();
        console.log(valor);
        general = $(this).parents('tr');
        idordenventa = general.find('input').attr('rel');
        campo = 'observacion_entregaProd';
//           console.log(general);
//           console.log(id);
        $.post('/facturacion/updateObservacion', {idordenventa: idordenventa, valor: valor, campo: campo}, function (data) {
            console.log(data);
        }, 'json');

    });
    $("#numcajas").keyup(function () {
        valor = $(this).val();
        general = $(this).parents('tr');
        idordenventa = general.find('input').attr('rel');
        campo = 'nrocajas';
        console.log(general);
//           console.log(id);
        $.post('/facturacion/updateObservacion', {idordenventa: idordenventa, valor: valor, campo: campo}, function (data) {

            //console.log(data);
        }, 'json');
    });

    $(".confirmacion").change(function () {
        general = $(this).parents('tr');
        valor = $(this).closest('select').find('option').filter(':selected:last').val();
        idordenventa = general.find('input').attr('rel');
        console.log(valor);
        muestraMensaje(idordenventa, valor);
        general.find('input').attr('disabled', true);
        $(this).attr('disabled', true);
        if (valor == "retornado1") {
            $(general).find('.despachos').append(
                    "<div class='separador'>" +
                    "Despacho2:<input type='checkbox' class='despachados' " + "onclick=muestraMensaje(" + idordenventa + ",'despacho2')>" +
                    "Fecha: </div>");

            $(general).find('.confirmaciones').append("<div class='separador'><select class='confirmacion' onchange='getval(this," + idordenventa + ");'><option value=''>Seleccionar</option><option value='entregado2'>Entregado</option><option value='retornado2'>Retornado</option><option value='anulado2'>Anulado</option></select>Fecha:</div>");
        }
        if (valor == "retornado2") {
            $(general).find('.despachos').append(
                    "<div class='separador'>" +
                    "Despacho3:<input type='checkbox' class='despachados' " + "onclick=muestraMensaje(" + idordenventa + ",'despacho3')>" +
                    "Fecha: </div>");

            $(general).find('.confirmaciones').append("<div class='separador'><select class='confirmacion' onchange='getval(this," + idordenventa + ");'><option value=''>Seleccionar</option><option value='entregado3'>Entregado</option><option value='anulado3'>Anulado</option></select>Fecha:</div>");
        }

    });
});
function observacion() {
    valor = $('.observacion').val();
    console.log(valor);
    general = $('.observacion').parents('tr');
    idordenventa = general.find('input').attr('rel');
    campo = 'observacion_entregaProd';
//           console.log(general);
//           console.log(id);
    $.post('/facturacion/updateObservacion', {idordenventa: idordenventa, valor: valor, campo: campo}, function (data) {
        console.log(data);

    }, 'json');
}

function MostrarOrdenVenta(idOrdenVenta) {
    console.log(idOrdenVenta);
    var ruta = "/facturacion/filtroDespacho/" + idOrdenVenta;
    $('#tblDespacho tbody').html('');
    $.post(ruta, function (data) {
        //$('#tblDespacho tbody').html(data);
        console.log(data);
        $('#tblDespacho tbody').append("<tr><td>" + data.codigov + "</td>" +
                "<td>" + data.simbolomoneda + ' ' + data.importeov + "</td>" +
                "<td>" + data.transporte + "</td>" +
                "<td><input type='text' id='numcajas' rel='" + data.idordenventa + "' value='" + data.nrocajas + " 'class='numeric required required-none' style='width: 35px;'></td>" +
                "<td>" + data.nombres + ' ' + data.apellidopaterno + ' ' + data.apellidomaterno + "</td>" +
                "<td>" + data.razonsocial + "</td>" +
                "<td>" + data.observaciones + "</td>" +
                "<td>" +
                "<label>Despacho1</label><input type='checkbox' rel='" + data.idordenventa + "' class='despachados' value='despacho_prod' " + (data.despacho1 == 1 ? "checked='checked'" : '') + " onclick=muestraMensaje(" + data.idordenventa + ",'despacho_prod')>" +
                "<label>Despacho2</label><input type='checkbox' rel='" + data.idordenventa + "' class='despachados' value='despacho_prod2' " + (data.despacho_prod2 == 1 ? "checked='checked'" : '') + " onclick=muestraMensaje(" + data.idordenventa + ",'despacho_prod2')>" +
                "<label>Despacho3</label><input type='checkbox' rel='" + data.idordenventa + "' class='despachados' value='despacho_prod3' " + (data.despacho_prod3 == 1 ? "checked='checked'" : '') + " onclick=muestraMensaje(" + data.idordenventa + ",'despacho_prod3')>" +
                "<label>Fecha:</label>" +
                "<label>" + data.fechadespachado + "</label>" +
                "</td>" +
                "<td>" +
                "<ul>" +
                "<li>" +
                "<label>Confirmado:</label>" +
                "<input type='checkbox' rel='" + data.idordenventa + "' class='confirmacion' value='confirmacion_prod'" + (data.confirmacion_prod == 1 ? "checked='checked'" : '') + " onclick=muestraMensaje(" + data.idordenventa + ",'confirmacion_prod') >" +
                "</li>" +
                "<li>" +
                "<label>Anulado:</label>" +
                "<input type='checkbox' rel='" + data.idordenventa + "' class='anulado' value='anulado'" + (data.anulado == 1 ? "checked='checked'" : '') + " onclick=muestraMensaje(" + data.idordenventa + ",'anulado')>" +
                "</li>" +
                "</ul>" +
                "<label>Observacion:</label>" +
                "<textarea class='observacion' onkeyup='observacion()'>" + data.observacion_entregaprod + "</textarea>" +
                "</td>"

                );

    }, 'json');
    //console.log(idOrdenVenta);

}
function muestraMensaje(idordenventa, campo) {

    $.post('/facturacion/updateDespacho', {idordenventa: idordenventa, campo: campo}, function (data) {
        console.log(data);
        if (data == true) {
            alert("datos actualizados");
        }

    }, 'json');
}
function muestraObservar(id) {
    $.ajax({
        url: '/ordenventa/detalle/',
        type: 'GET',
        data: {'IDOV': id},
        success: function (respuesta) {
            //console.log(respuesta);
            $(".detalleOV").hide('Blind');
            $("#tablita tbody").html(respuesta);

            $(".detalleOV").show('Blind');
        },
        error: function (error) {
            //console.log(error);
        }
    });

    return false;
}
function getval(sel, idordenventa) {
    campo = sel.value;
    console.log(idordenventa);
    $.post('/facturacion/updateDespacho', {idordenventa: idordenventa, campo: campo}, function (data) {
        console.log(data);
        if (campo == "retornado2") {
            $(general).find('.despachos').append(
                    "<div class='separador'>" +
                    "Despacho3<input type='checkbox' class='despachados'" + "onclick=muestraMensaje(" + idordenventa + ",'despacho3')>" +
                    "Fecha: </div>");
            $(general).find('.confirmaciones').append("<select class='confirmacion' onchange='getval(this," + idordenventa + ");'><option value=''>Seleccionar</option><option value='entregado3'>Entregado</option><option value='anulado3'>Anulado</option></select>Fecha:");
        }


    }, 'json');
}

