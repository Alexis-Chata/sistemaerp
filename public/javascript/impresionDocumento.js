$(document).ready(function () {
    var tipodoc;
    var numdocGeneral = "";
    var serieGeneral = "";
    var ruta;
    var iddocumentogeneral;

    $('#lstTipoDocumento').change(function () {
        lista = $(this).val();
        tipodoc = lista;
        mostrarInput(lista);
    });

    $('#txtOrdenVenta').autocomplete({
        source: "/ordenventa/PendientesxPagar/",
        select: function (event, ui) {
            $('#txtIdOrden').val(ui.item.id);
            Documento = ui.item.id;
            cargaDocumento(Documento, tipodoc);
        }
    });

    $('#txtNumeroLetra').autocomplete({
        source: "/ordenventa/busquedaletras/",
        select: function (event, ui) {
            $('#txtIdOrden').val(ui.item.id);
            Documento = ui.item.id;
            cargaDocumento(Documento, tipodoc);
        }
    });

    $('.cargar').live('click', function (e) {
        e.preventDefault();
        if (confirm('¿Desea Realmente Cargar El Documento?')) {
            padre = $(this).parents('tr');
            numdocGeneral = padre.find('.numdoc').val();
            serieGeneral = padre.find('.serie').val();
            iddocumentogeneral = $(this).data('id');
            orden = $('#txtIdOrden').val();
            ruta = rutaDocumentoText(tipodoc, orden, iddocumentogeneral);
            //console.log(iddocumentogeneral);
            console.log(numdocGeneral);
            console.log(serieGeneral);
            if (numdocGeneral != "" && serieGeneral != "" && tipodoc > 4 && tipodoc != 10 && numdocGeneral != undefined) {
                console.log(tipodoc);
                actualizaDocumento(iddocumentogeneral, numdocGeneral, serieGeneral, ruta);
            } else if (numdocGeneral != "" && serieGeneral != "") {
                console.log('ruta: ' + ruta);
                CargarArchivos(ruta, iddocumentogeneral);
            } else {
                alert('Ingrese La Serie y Numero de Documento');
            }
        }
    });

    $('.lupaVer').live('click', function (e) {
        e.preventDefault();
        padre = $(this).parents('tr');
        numdocGeneral = padre.find('.numdoc').val();
        serieGeneral = padre.find('.serie').val();
        iddocumentogeneral = $(this).attr('id');
        orden = $('#txtIdOrden').val();
        ruta = rutaDocumentoVer(tipodoc, orden, iddocumentogeneral);
        abrirVentana(ruta);
    });

    $('.imprimir').live('click', function (e) {
        e.preventDefault();
        if (confirm('¿Desea Realmente Imprimira?')) {
            padre = $(this).parents('tr');
            numdocGeneral = padre.find('.numdoc').val();
            serieGeneral = padre.find('.serie').val();
            iddocumentogeneral = $(this).attr('id');
            noimprimir = $('#noimprimir').val()?$('#noimprimir').val():0;
            dist_prov_depa = $('#dist_prov_depa').val();
            id_fecha_exacta = $('#id_fecha_exacta').val();
            orden = $('#txtIdOrden').val();
            ruta = rutaDocumento(tipodoc, orden, iddocumentogeneral, noimprimir, dist_prov_depa, id_fecha_exacta);
            //console.log(iddocumentogeneral);
            console.log(numdocGeneral);
            console.log(serieGeneral);
            if (numdocGeneral != "" && serieGeneral != "" && tipodoc > 4 && numdocGeneral != undefined) {
                actualizaDocumento(iddocumentogeneral, numdocGeneral, serieGeneral, ruta);
            } else if (numdocGeneral != "" && serieGeneral != "") {
                abrirVentana(ruta);
                console.log('entro: ' + ruta);
            } else {
                alert('Ingrese La Serie y Numero de Documento');
            }
        }
    });

    $('.anular').live('click', function (e) {
        e.preventDefault();
        padre = $(this).parents('tr');
        iddocumentogeneral = padre.find('.iddocumento').val();
        if (confirm('¿Esta Seguro de Anular?')) {
            anularDocumento(iddocumentogeneral, tipodoc);
        }
    });
    $('#lstTipoDocumento').focus();
});

function abrirVentana(ruta) {
    window.open(ruta, "Mi pagina", "width=900,height=500,menubar=no,location=no,resizable=no");
}

function cargaDocumento(Documento, tipodoc) {
    console.log(tipodoc);
    $.ajax({
        url: '/documento/documentosxOrden',
        type: 'post',
        datatype: 'html',
        data: {'idordenventa': Documento, 'tipodoc': tipodoc},
        success: function (resp) {
            $('#contenedorDocumentos').html(resp);
            if ($('#lstTipoDocumento').val() == 1) {
                $('#bloquepercepcion').attr('style', 'float: right');
            } else {
                $('#bloquepercepcion').attr('style', 'display: none; float: right');
            }
        }
    });
}

function CargarArchivos(ruta, iddocumentogeneral) {
    $.ajax({
        url: ruta,
        type: 'post',
        dataType: "json",
        data: {'iddocumentogeneral': iddocumentogeneral, 'percepcion': $('#lstValorPercepcion').val()},
        success: function (datos) {
            if (datos['rspta'] == 1) {
                $('#iconofe' + iddocumentogeneral).attr('src', '/imagenes/impfebien.png');
                $('#idBlockSinCargar' + iddocumentogeneral).html(datos['correlativo']);
            } else if (datos['rspta'] == 2) {
                $.msgbox('Mensaje: ', 'El documento ya ha sido cargado.');
                execute();
            } else {
                $.msgbox('Mensaje: ', 'El documento no pudo ser cargada.');
                execute();
            }
        }, error: function (a, b, c) {
            console.log(a);
            console.log(b);
            console.log(c);
        }
    });
}
function mostrarInput(lista) {
    if (lista == 7) {
        $('#txtOrdenVenta').hide();
        $('#txtNumeroLetra').show();
    } else if (lista == 0) {
        $('#txtOrdenVenta').hide();
        $('#txtNumeroLetra').hide();
    } else {
        $('#txtOrdenVenta').show();
        $('#txtNumeroLetra').hide();
    }
    $('#txtOrdenVenta').val('');
    $('#txtNumeroLetra').val('');
}

function rutaDocumentoText(lista, idordenventa, iddocumentogeneral) {
    if (lista == 1) {
        return rut = '/documento/generaFacturaTXT';
    } else if (lista == 2) {
        return rut = '/documento/generaBoletaTXT';
    } else if (lista == 4) {
        return rut = '/documento/generaGuiaTXT';
    } else if (lista == 5) {
        return rut = '/documento/generaNotaCreditoTXT';
    } else if (lista == 6) {
        return rut = '/documento/generaNotaDebitoTXT';
    } else if (lista == 7) {
        return rut = '/documento/generaLetraTXT';
    } else if (lista == 10) {
        return rut = '/documento/generarPercepcionTxt';
    }
}

function rutaDocumentoVer(lista, idordenventa, iddocumentogeneral) {
    if (lista == 1) {
        return rut = '/documento/verFactura/' + iddocumentogeneral;
    } else if (lista == 2) {
        return rut = '/documento/verBoleta/' + iddocumentogeneral;
    } else if (lista == 5) {
        return rut = '/documento/verNotaCredito/' + iddocumentogeneral;
    } else if (lista == 6) {
        return rut = '/documento/verNotaDebito/' + iddocumentogeneral;
    }
}

function rutaDocumento(lista, idordenventa, iddocumentogeneral, noimprimir, dist_prov_depa, id_fecha_exacta) {
    if (lista == 1) {
        if (id_fecha_exacta.length > 0) {
            id_fecha_exacta = id_fecha_exacta.toString().replace('/', '.');
            id_fecha_exacta = id_fecha_exacta.toString().replace('/', '.');
        }
        
        return rut = '/documento/generaFactura/' + iddocumentogeneral + '/' + $('#lstValorPercepcion').val() + '/' + id_fecha_exacta;
    } else if (lista == 2) {
        return rut = '/documento/generaBoleta/' + iddocumentogeneral;
    } else if (lista == 4) {console.log('/documento/generaGuia/' + iddocumentogeneral + '/' + noimprimir + '/' + dist_prov_depa);
        return rut = '/documento/generaGuia/' + iddocumentogeneral + '/' + noimprimir + '/' + dist_prov_depa;
    } else if (lista == 5) {
        return rut = '/documento/generaNotaCredito/' + iddocumentogeneral;
    } else if (lista == 6) {
        return rut = '/documento/generaNotaDevito/' + iddocumentogeneral;
    } else if (lista == 7) {
        return rut = '/documento/generaLetra/' + iddocumentogeneral;
    } else if (lista == 10) {
        return rut = '/documento/generarPercepcionTxt/' + iddocumentogeneral;
    }
}
function actualizaDocumento(iddocumento, numerodocumento, seriedocumento, ruta) {
    $.ajax({
        url: '/documento/actualizaDocumentoJson',
        type: 'post',
        datatype: 'html',
        data: {'iddocumento': iddocumento, 'numdoc': numerodocumento, 'serie': seriedocumento},
        success: function (resp) {
            console.log(resp);
            abrirVentana(ruta);
        }
    });
}
function anularDocumento(iddocumento, tipodoc) {
    $.ajax({
        url: '/documento/anularDocumentos',
        type: 'post',
        datatype: 'html',
        data: {'iddocumento': iddocumento},
        success: function (resp) {
            console.log(resp);
            valorDocumento = $('#txtIdOrden').val();
            cargaDocumento(valorDocumento, tipodoc);
        }
    });
}
