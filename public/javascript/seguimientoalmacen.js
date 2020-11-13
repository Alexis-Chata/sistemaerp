$(document).on('ready', function () {
    $('#txtBusqueda').autocomplete({
        source: "/facturacion/filtro/",
        select: function (event, ui) {
            var idOrdenVenta = ui.item.id;
            valoridordenventa = ui.item.id;
            MostrarOrdenVenta(idOrdenVenta);
        }
    });
    $('#txtBuscar').autocomplete({
        source: "/facturacion/filtro/",
        select: function (event, ui) {
            var idOrdenVenta = ui.item.id;
            valoridordenventa = ui.item.id;
            MostrarConfirmacionDespacho(idOrdenVenta);
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
    $(".entregado").live('click', function () {
        campo=$(this).val();
        padre = $(this).parents('tr');
        idordenventa = padre.find('input').attr('rel');
        console.log(idordenventa);
        console.log(campo);
       
        $.post('/seguimiento/updateConfirmacion', {idordenventa: idordenventa,campo:campo}, function (data) {
             
                console.log(data);
                if (data == true) {
                    alert("datos actualizados");

                }
        });


    });
//    $(":checkbox").live('click', function () {
//        idordenventa = $(this).attr('rel');
//        campo = $(this).val();
//        valor = "";
//        $.post('/seguimiento/updateSeguimiento', {idordenventa: idordenventa, campo: campo, valor: valor}, function (data) {
//            if (data == true) {
//                alert("datos actualizados");
//
//            }
//        }, 'json');
//
//    });
    $(".observacion").keyup(function () {
        valor = $(this).val();
        console.log(valor);
        general = $(this).parents('tr');
        idordenventa = general.find('input').attr('rel');
        campo = 'observacion_entregaProd';
        $.post('/facturacion/updateObservacion', {idordenventa: idordenventa, valor: valor, campo: campo}, function (data) {
            console.log(data);
        }, 'json');
    });
    $(".confirmacion").live("change", function () {
        general = $(this).parents('tr');
        valor = $(this).closest('select').find('option').filter(':selected:last').val();
        campo = $(this).closest('select').attr('name');
        idordenventa = general.find('input').attr('rel');
        console.log(campo);
        console.log(valor);
        muestraMensaje(idordenventa, campo, valor);
        general.find('input').attr('disabled', true);
        $(this).attr('disabled', true);
        if (valor == 'R1') {
            $(general).find('.confirmaciones').append("<div class='separador'><select class='confirmacion' name='confirmacion'><option value=''>Seleccionar</option><option value='E2'>Entregado</option><option value='R2'>Retornado</option><option value='ED2'>Entregado/Devolucion</option><option value='A2'>Anulado</option><option value='ED2'>Anulado</option></select>Fecha:</div>");
        }
        if (valor == 'R2') {
            $(general).find('.confirmaciones').append("<div class='separador'><select class='confirmacion' name='confirmacion'><option value=''>Seleccionar</option><option value='E3'>Entregado</option><option value='A3'>Anulado</option><option value='ED3'>Entregado/Devolucion</option></select>Fecha:</div>");
        }

    });
    $("#btnConsultar").click(function () {
        fechaSeguimiento = $('#fechaSeguimiento').val();
        cargarDetalles(fechaSeguimiento);

    });
    $('#btnImprimir').click(function (e) {
        e.preventDefault();
        $('th').css('color:green;text-align: center;');
        $('.center').css('text-align: center;');
        $('.seguimiento').css('background-color: red;')
        imprSelec('contenedor');
    });
});
function MostrarOrdenVenta(idOrdenVenta) {
    console.log(idOrdenVenta);
    var ruta = "/seguimiento/seguro";
    $('#tblDespacho tbody').html('');
    $.post(ruta, {idOrdenVenta: idOrdenVenta}, function (data) {
        $('#txtBusqueda').val('');
        $('#txtBusqueda').focus();
        console.log(data);
       // console.log(data.seguimiento[0].confirmacion);
        var text = "";
        var dato="";
 
        if(data.seguimiento === null){
           $('#tblDespacho tbody').append("<tr><td>" + data.factura[0].codigov + "</td>" +
               
                "<td>" + data.factura[0].nombres + " " + data.factura[0].apellidopaterno + "" + data.apellidomaterno + "</td>" +
                "<td>" + data.factura[0].razonsocial + "<input type='hidden' rel='" + idOrdenVenta + "'/></td>"+
                "<td class='confirmaciones'><select name='confirmacion' class='confirmacion'" +  ">" +
                "<option value='seleccionar' class='confirmacion'>Seleccionar</option>" +
                "<option  value='E1'>Entregado</option>" +
                "<option  value='R1'>Retornado</option>" +
                "<option  value='A1'>Anulado</option>" +
                "<option  value='ED1'>Entregado/Devolucion</option>" +
                "</select><label>Fecha Registro:</label>" + text +
                "</td>"
                );
       }
       if(data.seguimiento.length && data.factura.length){
           var num = 0; var ultimo = "";
            for(var i=0; i< data.seguimiento.length;i++){
                //text +="valor" + data.seguimiento[i].confirmacion;
                    num=i+1
                    valor='E'+ num;
                    text += "<div class='separador'><select name='confirmacion2' class='confirmacion' " + (data.seguimiento[i].confirmacion == 'E'+ num || data.seguimiento[i].confirmacion == 'A'+ num || data.seguimiento[i].confirmacion == 'R'+ num || data.seguimiento[i].confirmacion == 'ED'+ num ? "disabled='disabled'" : '') +">" +
                              "<option value='seleccionar' class='confirmacion' >Seleccionar</option>" +
                              "<option value="+ 'E'+ num + ' '+ (data.seguimiento[i].confirmacion == 'E'+ num ? "selected='selected'" : '') + "> Entregado</option>" +
                              "<option value="+ 'R'+ num + ' ' + (data.seguimiento[i].confirmacion == 'R'+ num ? "selected='selected'" : '') + "> Retornado</option>" +
                              "<option value="+ 'A'+ num + ' ' +(data.seguimiento[i].confirmacion == 'A'+ num ? "selected='selected'" : '') + "> Anulado</option>" +
                              "<option value="+ 'ED'+ num + ' ' +(data.seguimiento[i].confirmacion == 'ED'+ num ? "selected='selected'" : '') + "> Entregado/Devolucion</option>" +
                              "</select><label>Fecha Registro:</label></div>";
                      console.log(text);
                      ultimo = data.seguimiento[i].confirmacion;
                      
                    /*if(data.seguimiento[i].confirmacion == 'R1' && data.seguimiento.length==1){
                      text += "<div class='separador'><select name='confirmacion' class='confirmacion'" +(data.seguimiento[i].confirmacion == 'E2' || data.seguimiento[i].confirmacion == 'A2' || data.seguimiento[i].confirmacion == 'R2' || data.seguimiento[i].confirmacion == 'ED2'? "disabled='disabled'" : '')+ ">" +
                              "<option value='seleccionar'>Seleccionar</option>" +
                              "<option value='E2'> Entregado</option>" +
                              "<option value='R2'> Retornado</option>" +
                              "<option value='A2'> Anulado</option>" +
                              "<option value='ED2'> Entregado/Devolucion </option>" +
                              "</select></div>";
                    }
                    if(data.seguimiento[i].confirmacion == 'R2' && data.seguimiento.length==2){
                      text += "<div class='separador'><select name='confirmacion3' class='confirmacion'" +(data.seguimiento[i].confirmacion == 'E3' || data.seguimiento[i].confirmacion == 'A3' ||  data.seguimiento[i].confirmacion == 'ED3' ? "disabled='disabled'" : '')+" >" +
                              "<option value='seleccionar'>Seleccionar</option>" +
                              "<option value='E3'> Entregado</option>" +
                              "<option value='R3'> Retornado</option>" +
                              "<option value='A3'> Anulado</option>" +
                              "<option value='ED3> Entregado/Devolucion</option>" +
                              "</select><label>Fecha Registro:</label></div>";
                    }*/
                }
                console.log(ultimo+''+num);
                if (ultimo == "" || ultimo == 'R'+num) {
                    num++;
                    text += "<div class='separador'><select name='confirmacion4' class='confirmacion' " +">" +
                              "<option value='seleccionar' class='confirmacion' >Seleccionar</option>" +
                              "<option value="+ 'E'+ num + "> Entregado</option>" +
                              "<option value="+ 'R'+ num + "> Retornado</option>" +
                              "<option value="+ 'A'+ num + "> Anulado</option>" +
                              "<option value="+ 'ED'+ num + "> Entregado/Devolucion</option>" +
                              "</select><label>Fecha Registro:</label></div>";
                }

            $('#tblDespacho tbody').append("<tr><td>" + data.factura[0].codigov + "</td>" +
                   
                    "<td>" + data.factura[0].nombres + " " + data.factura[0].apellidopaterno + "" + data.factura[0].apellidomaterno + "</td>" +
                    "<td>" + data.factura[0].razonsocial + "<input type='hidden' rel='" + idOrdenVenta + "'/></td>" +
                    "<td class='confirmaciones'>" + text +
                    "</td>"
             );
        }  
        
    }, 'json');
}
function muestraMensaje(idordenventa, campo, valor) {
    $.post('/seguimiento/updateSeguimiento', {idordenventa: idordenventa, campo: campo, valor: valor}, function (data) {
        console.log(data);
        if (data == true) {
            alert("datos actualizados");
        }
    }, 'json');
}
function cargarDetalles(fechaSeguimiento) {
    
    $.ajax({
        url: '/seguimiento/listarSeguimiento',
        type: 'post',
        datatype: 'html',
        data: {'fechaSeguimiento': fechaSeguimiento},
        success: function (resp) {
            $('#tblSeguimiento').html(resp);
        }
    });
}
function MostrarConfirmacionDespacho(idOrdenVenta) {
    console.log(idOrdenVenta);
    var ruta = "/seguimiento/seguro";
    $.post(ruta, {idOrdenVenta: idOrdenVenta}, function (data) {
        $('#txtBuscar').val('');
        $('#txtBuscar').focus();
         $('#tblConfirmacion tbody').html('');
        console.log(data);
        var text = "";
        $('#tblConfirmacion tbody').append("<tr><td>" + data.codigov + "</td>" +
                "<td>" + data.simbolomoneda + " " + data.importeov + "</td>" +
                "<td>" + data.nombres + " " + data.apellidopaterno + "" + data.apellidomaterno + "</td>" +
                "<td>" + data.razonsocial + "<input type='hidden' rel='" + idOrdenVenta + "'/></td>" +
                "<td>" +
                "<input type='checkbox' rel='" + data.idordenventa + "' class='entregado' " + (data.confirmacionentrega == 1 ? "checked='checked'" : '') + "value='confirmacionentrega'" + ">" +
                "<label>Observacion:</label>" +
                "<textarea class='observacion'>" + data.observacion + "</textarea>" +
                "</td>" +
                "</tr>");
    }, 'json');
}
