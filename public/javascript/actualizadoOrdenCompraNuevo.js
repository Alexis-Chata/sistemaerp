$(document).ready(function () {

    if ($('#ocActualizado').val() == 1) {
        $('input').attr('disabled', 'disabled');
        $('select').attr('disabled', 'disabled');
        $('input').addClass('colorazul');
        $('select').addClass('colorazul');
    }

    $('#lstTipoCompra').change(function () {
        if ($(this).val() == 1) {
            $('#textComprobante').html('#DUA: ');
        } else {
            $('#textComprobante').html('Factura: ');
        }
    });

    $('#tblAsociados').on('click', '.EliminarOC', function () {
        $('.ColDoc_' + $(this).data('id')).remove();
        $('#idOrdenCompra_' + $(this).data('id')).remove();
        item = $(this).parents('tr');
        item.remove();
        calcularConceptos();
        recalcularDetalles();
        return false;
    });

    $('#txtOrdenCompra').autocomplete({
        source: "/ordencompra/autoCompleteCompraXDuaNuevo/",
        select: function (event, ui) {
            if ($('#idOrdenCompra').val() == ui.item.id) {
                alert('No puedes asociar una orden de compra a si misma.');
            } else if ($('#idOrdenCompra_' + ui.item.id).length) {
                alert('La Orden de Compra ' + ui.item.label + ' ya ha sido añadida.');
            } else {
                var estado = "CONFIRMADO";
                if (ui.item.vbimportaciones == 0) {
                    estado = "<i>Sin Confirmar</i>";
                }
                var contenido = '<tr>'
                        + '<th colspan="2">Orden Compra: </th><td colspan="3">' + ui.item.label + '</td>'
                        + '<th colspan="3">Proveedor: </th><td colspan="3">' + ui.item.proveedor + '</td>'
                        + '<th colspan="3">Estado: </th><td colspan="4">' + estado + '</td>'
                        + '<th style="background: skyblue;"><a href="#" title="Eliminar Orden de Compra" class="EliminarOC" data-id="' + ui.item.id + '"><img src="/imagenes/error.png" width="15px"></a></th>'
                        + '</tr>';
                anadirDetalledeOC(ui.item.id);
                $('#tblAsociados thead').append(contenido);
            }
        }
    });

    $('#blocIdOC').hide();

    $('#tipocambiograbado').change(function (e) {
        if ($(this).val().length == 0 || $(this).val() < 0) {
            $(this).val('0.00');
        }
        var valorensoles = parseFloat($('#txtTotalimportevalorizadoocOCA').val()) * parseFloat($(this).val());
        $(this).val(parseFloat($(this).val()).toFixed(2));
        $('#txtTotalimportevalorizadoocOCSoles').val(valorensoles.toFixed(2));
    });

    $('#btnConfirmarDua').click(function () {
        var bandera = 1;
        if ($('#lstTipoCompra').val() == 1) {
            if ($('#nroDua').val().length == 0) {
                bandera = 0;
            }
        }
        if (bandera == 1) {
            if (confirm('¿Esta seguro de confirmar, una vez grabado no va poder modificar la Estructura de Costos?')) {
                $('#frmEstructuraCostos').submit();
            }
        } else {
            $('#nroDua').focus();
        }
        return false;
    });

    $('#tblDetalleOrdenCompra').on('click', '.eliminarProducto', function () {
        item = $(this).parents('tr');
        item.remove();
        calcularConceptos();
        recalcularDetalles();
        return false;
    });
    
    $('#tblDetalleOrdenCompra').on('blur', '.txtPiezas', function () {
        var elementParent = $(this).parents('tr');
        var cantidad = parseInt(elementParent.find('.txtCantidadDetalle').val());
        var piezas = $(this).val();
        var carton = (piezas != '') ? (parseInt(cantidad) / parseInt(piezas)) : '';
        elementParent.find('.txtCarton').val(carton);
        calcularConceptos();
    });

    $('#tblDetalleOrdenCompra').on('blur', '.txtCantidadDetalle, .txtfobDetalle', function () {
        //$('.txtCantidadDetalle, .txtVolumen, .txtfobDetalle').blur(function () {
        /*
        item = $(this).parents('tr');
        item.find('input').addClass('colorazul');
        clase = $(this).attr('class');
        if (clase == 'txtVolumen numeric required colorazul') {
            for (var i = 1; i <= 2; i++) {
                valor = $('#txtFleteOC').val();
                calcularParteConceptos(('.txtVolumenDetalle'), ('.txtFleteDetalle'), valor);
                recalcularDetalles();
            }
        } else {
            for (var i = 1; i <= 2; i++) {
                calcularParteConceptos(('.txtVolumenDetalle'), ('.txtFleteDetalle'), $('#txtFleteOC').val());
                calcularParteConceptos(('.txtfobTotalDetalle'), ('.txtSeguroDetalle'), $('#txtSeguroOC').val());
                calcularParteConceptos(('.txtfobTotalDetalle'), ('.txtTasaDespachoDetalle'), $('#txtTotalcostotasadespOC').val());
                calcularParteConceptos(('.txtfobTotalDetalle'), ('.txtFlatDetalle'), $('#txtTotalcostoflatOC').val());
                calcularParteConceptos(('.txtfobTotalDetalle'), ('.txtVBDetalle'), $('#txtTotalcostoalmacenvbOC').val());
                calcularParteConceptos(('.txtfobTotalDetalle'), ('.txtGateInDetalle'), $('#txtTotalcostoalmacengateOC').val());
                calcularParteConceptos(('.txtfobTotalDetalle'), ('.txtCV1Detalle'), $('#txtTotalcostocv1OC').val());
                calcularParteConceptos(('.txtfobTotalDetalle'), ('.txtCV2Detalle'), $('#txtTotalcostocv2OC').val());
                calcularParteConceptos(('.txtfobTotalDetalle'), ('.txtCV3Detalle'), $('#txtTotalcostocv3OC').val());
                calcularParteConceptos(('.txtfobTotalDetalle'), ('.txtAgenteAduanaDetalle'), $('#txtTotalcomisionagenteaduOC').val());
                calcularParteConceptos(('.txtfobTotalDetalle'), ('.txtFleteInternoDetalle'), $('#txtTotalcostofleteinternoOC').val());
                recalcularDetalles();
            }
        }*/
        recalcularFobDetalle();
        calcularConceptos();
    });
    
    $('#txtFleteOC').on('blur', function () {
        var totalConceptoPorcentaje = totalConcepto('.txtCarton');
        calcularParteConceptos('.txtCarton', '.txtFleteDetalle', $(this).val(), totalConceptoPorcentaje);
        recalcularFobDetalle();
    });
    
    $('#txtSeguroOC').on('blur', function () {
        calcularParteConceptos('.txtfobTotalDetalle', '.txtSeguroDetalle', $(this).val(), $('#txtFobTotalOC').val());
        recalcularFobDetalle();
    });
    
    $('#txtTotalSada').on('blur', function () {
        calcularParteConceptos('.txtfobTotalDetalle', '.txtSada', $(this).val(), $('#txtFobTotalOC').val());
        recalcularFobDetalle();
    });
    
    $('#txtTotalSscto').on('blur', function () {
        calcularParteConceptos('.txtfobTotalDetalle', '.txtSscto', $(this).val(), $('#txtFobTotalOC').val());
        recalcularFobDetalle();
    });
    
    $('#txtTotalFargo').on('blur', function () {
        calcularParteConceptos('.txtfobTotalDetalle', '.txtFargo', $(this).val(), $('#txtFobTotalOC').val());
        recalcularFobDetalle();
    });
    
    $('#txtTotalVoBo').on('blur', function () {
        calcularParteConceptos('.txtfobTotalDetalle', '.txtVBDetalle', $(this).val(), $('#txtFobTotalOC').val());
        recalcularFobDetalle();
    });
        
    $('#txtTotalDevctndr').on('blur', function () {
        calcularParteConceptos('.txtfobTotalDetalle', '.txtDevctndr', $(this).val(), $('#txtFobTotalOC').val());
        recalcularFobDetalle();
    });
        
    $('#txtTotalFleteinterno').on('blur', function () {
        var totalConceptoPorcentaje = totalConcepto('.txtCarton');
        calcularParteConceptos('.txtCarton', '.txtFleteInternoDetalle', $(this).val(), totalConceptoPorcentaje);
        recalcularFobDetalle();
    });
    
    $('#txtTotalAgenteaduanas').on('blur', function () {
        var totalConceptoPorcentaje = totalConcepto('.txtciftotal');
        calcularParteConceptos('.txtciftotal', '.txtAgenteAduanaDetalle', $(this).val(), totalConceptoPorcentaje);
        recalcularFobDetalle();
    });

    $('.txtadvalorporcentaje').blur(function () {
        item = $(this).parents('tr'); 
        var porcentaje = parseFloat($(this).val())/100;
        var cif = parseFloat(item.find('.txtciftotal').val());
        item.find('.txtadvaloremvalor').val(cif*porcentaje);     
        recalcularxFila(item);
        $('#txtTotalAdvaloremvalor').val(totalConcepto('.txtadvaloremvalor'));
    });
            
    $('#PrintTodo').on('click', function (e) {
        e.preventDefault();
        imprSelec('PrintdetalleOrdenCompra');
    });
    
    $('#txtTipoCambioGrabado').change(function (e) {
        if ($(this).val().length == 0 || $(this).val() < 0) {
            $(this).val('0.00');
        }
        var valorensoles = parseFloat($('#txtTotalimportevalorizadoocOCA').val()) * parseFloat($(this).val());
        $(this).val(parseFloat($(this).val()).toFixed(2));
        $('#txtTotalimportevalorizadoocOCSoles').val(valorensoles.toFixed(2));
    });

});

function anadirDetalledeOC(idordencompra) {
    $.ajax({
        url: '/ordencompra/listardetalleXocNuevo',
        type: 'post',
        datatype: 'json',
        data: {'idordencompra': idordencompra, 'cantidad': $('#tblDetalleOrdenCompra').data('cantidad')},
        success: function (resp) {
            $('#txtOrdenCompra').val('');
            $('#blocIdOC').append('<input type="hidden" value="' + idordencompra + '" id="idOrdenCompra_' + idordencompra + '" name="idordenescompras[]">');
            $('#tblDetalleOrdenCompra').data('cantidad', resp['cantidad']);
            $('#tblDetalleOrdenCompra tbody').append(resp['contenido']);
            calcularConceptos();
            recalcularDetalles();
        }, error: function (error) {
            console.log('error');
        }
    })
}

function calcularParteConceptos(concepto, conceptoPorcentaje, totalconcepto, totalConceptoPorcentaje) {
    var divisionconcepto = parseFloat(totalconcepto)/parseFloat(totalConceptoPorcentaje);
    var cont = 0;
    $(concepto).each(function () {
        valorconcepto = (parseFloat($(this).val().replace(',', ''))) * divisionconcepto;
        $(conceptoPorcentaje + ':eq(' + cont + ')').val(valorconcepto.toFixed(2));
        cont++;
    });
}

function recalcularFobDetalle() {
    var porcetajetotal = 0;
    var fobTotaldetalle = 0;
    var nro = 0;
    $('.txtfobDetalle').each(function () {
        element = $(this).parents('tr');
        recalcularxFila(element);
        fobTotaldetalle += parseFloat(element.find('.txtfobTotalDetalle').val());        
        porcetajetotal = porcetajetotal + parseFloat(element.find('.txtPorcentajeDetalle').val());
        nro ++;
    });
    $('#txtFobTotalOC').val(fobTotaldetalle.toFixed(2));
    if (nro > 0) {
        porcetajetotal = porcetajetotal/nro;
    }    
    $('#txtTotalpromedioporc').val(porcetajetotal.toFixed(2));
}

function recalcularDetalles() {
    var totalFobOC = 0;
    $('.txtfobTotalDetalle').each(function () {
        element = $(this).parents('tr');
        recalcularxFila(element);
        totalFobOC += parseFloat($(this).val());
    });
    $('#txtFobTotalOC').val(totalFobOC.toFixed(2));
}

function recalcular(element) {
    //Costos Fijos
    var volumenUnitario = parseFloat(element.find('.txtVolumen').val());
    var piezas = parseFloat(element.find('.piezas').val());
    var fobunitario = parseFloat(element.find('.txtfobDetalle').val());
    var cantidad = parseFloat(element.find('.txtCantidadDetalle').val());
    var carton = cantidad / piezas;
    var nuevoSubTotalFob = fobunitario * cantidad;
    element.find('.txtfobTotalDetalle ').val(nuevoSubTotalFob.toFixed(2));
    element.find('.carton').val(carton);
    var nuevoCBM = volumenUnitario * carton;
    element.find('.txtVolumenDetalle').val(nuevoCBM.toFixed(2));
    var flete = parseFloat(element.find('.txtFleteDetalle').val());
    $('#txtFleteOC').val(totalConcepto('.txtFleteDetalle').toFixed(2));
    var seguro = parseFloat(element.find('.txtSeguroDetalle').val());
    $('#txtSeguroOC').val(totalConcepto('.txtSeguroDetalle').toFixed(2));
    var cif = (fobunitario * cantidad + flete + seguro);
    var cifunitario = (cif / cantidad);
    element.find('.txtciftotal').val(cif.toFixed(2));
    element.find('.txtcifunitario').val(cifunitario.toFixed(2));
    var cifTotalOC = totalConcepto('.txtciftotal');
    $('#txtTotalCifOC').val(cifTotalOC.toFixed(2));

    var advaloremp = parseFloat(element.find('.txtAdvaloremPDetalle').val());
    var advaloremv = (cif * advaloremp) / 100;
    //$('#txtTotaladvaloremOC').val(totalConcepto('.txtAdvaloremVDetalle').toFixed(2));
    var tasadespacho = parseFloat(element.find('.txtTasaDespachoDetalle').val());
    //$('#txtTotalcostotasadespOC').val(totalConcepto('.txtTasaDespachoDetalle').toFixed(2));
    var fleteinterno = parseFloat(element.find('.txtFleteInternoDetalle').val());
    //$('#txtTotalcostofleteinternoOC').val(totalConcepto('.txtFleteInternoDetalle').toFixed(2));
    /*	var cf1=parseFloat(element.find('.txtCF1Detalle').val());
     $('#txtTotalcostocf1OC').val(totalConcepto('.txtCF1Detalle').toFixed(2));
     var cf2=parseFloat(element.find('.txtCF2Detalle').val());
     $('#txtTotalcostocf2OC').val(totalConcepto('.txtCF2Detalle').toFixed(2));*/
    //element.find('.txtAdvaloremVDetalle').val(advaloremv.toFixed(2));
    //var costosfijos=cif+advaloremv+tasadespacho+fleteinterno+cf1+cf2;
    var costosfijos = cif + advaloremv + tasadespacho + fleteinterno;
    //Costos Variables
    console.log(costosfijos);
    var Flat = parseFloat(element.find('.txtFlatDetalle').val());
    //$('#txtTotalcostoflatOC').val(totalConcepto('.txtFlatDetalle').toFixed(2));
    var VB = parseFloat(element.find('.txtVBDetalle').val());
    //$('#txtTotalcostoalmacenvbOC').val(totalConcepto('.txtVBDetalle').toFixed(2));
    var GateIn = parseFloat(element.find('.txtGateInDetalle').val());
    //$('#txtTotalcostoalmacengateOC').val(totalConcepto('.txtGateInDetalle').toFixed(2));
    /*var BoxFee=parseFloat(element.find('.txtBoxFeeDetalle').val());
     //$('#txtTotalcostoboxfeeOC').val(totalConcepto('.txtBoxFeeDetalle').toFixed(2));
     var InsuranceFee=parseFloat(element.find('.txtInsuranceFeeDetalle').val());
     $('#txtTotalcostoinsurancefeeOC').val(totalConcepto('.txtInsuranceFeeDetalle').toFixed(2));
     var Sobreestadia=parseFloat(element.find('.txtSobreestadiaDetalle').val());
     $('#txtTotalcostosobreestadiaOC').val(totalConcepto('.txtSobreestadiaDetalle').toFixed(2));
     var DocFee=parseFloat(element.find('.txtDocFeeDetalle').val());
     $('#txtTotalcostodocfeeOC').val(totalConcepto('.txtDocFeeDetalle').toFixed(2));
     var GasAdm=parseFloat(element.find('.txtGasAdmDetalle').val());
     $('#txtTotalcostogastosadministrativosOC').val(totalConcepto('.txtGasAdmDetalle').toFixed(2));*/
    var AgenteAduana = parseFloat(element.find('.txtAgenteAduanaDetalle').val());
    //$('#txtTotalcomisionagenteaduOC').val(totalConcepto('.txtAgenteAduanaDetalle').toFixed(2));
    var CV1 = parseFloat(element.find('.txtCV1Detalle').val());
    //$('#txtTotalcostocv1OC').val(totalConcepto('.txtCV1Detalle').toFixed(2));
    var CV2 = parseFloat(element.find('.txtCV2Detalle').val());
    //$('#txtTotalcostocv2OC').val(totalConcepto('.txtCV2Detalle').toFixed(2));
    var CV3 = parseFloat(element.find('.txtCV3Detalle').val());
    //$('#txtTotalcostocv3OC').val(totalConcepto('.txtCV3Detalle').toFixed(2));
    //var costosvariables=Flat+VB+GateIn+BoxFee+InsuranceFee+Sobreestadia+DocFee+GasAdm+AgenteAduana+CV1+CV2;
    var costosvariables = Flat + VB + GateIn + AgenteAduana + CV1 + CV2 + CV3;
    //calculos TOTALES:
    //console.log(costosfijos);
    //console.log(costosvariables);
    var totalItem = costosfijos + costosvariables;
    console.log(costosvariables);
    var totalunitarioitem = totalItem / cantidad;
    var porcentaje = ((totalunitarioitem - fobunitario) / fobunitario) * 100;
    element.find('.txtTotalDetalle').val(totalItem.toFixed(2));
    element.find('.txtTotalUnitarioDetalle').val(totalunitarioitem.toFixed(2));
    element.find('.txtPorcentajeDetalle').val(porcentaje.toFixed(2));
    $('#txtTotalimportevalorizadoocOC').val(totalConcepto('.txtTotalDetalle').toFixed(2));
    var totalOC = $('#txtTotalimportevalorizadoocOC').val();
    var tipocambio = $('#tipocambiograbado').val();
    var totalSolesOC = totalOC * tipocambio;
    $('#txtTotalimportevalorizadoocOCA').val($('#txtTotalimportevalorizadoocOC').val());
    $('#txtTotalimportevalorizadoocOCSoles').val(totalSolesOC.toFixed(2));
}

function recalcularxFila(element) {
    //Costos Fijos
    var carton = parseFloat(element.find('.txtCarton').val());
    var piezas = parseFloat(element.find('.txtPiezas').val());
    var fobunitario = parseFloat(element.find('.txtfobDetalle').val());
    var cantidad = parseFloat(element.find('.txtCantidadDetalle').val());
    
    var nuevoSubTotalFob = fobunitario * cantidad;
    element.find('.txtfobTotalDetalle ').val(nuevoSubTotalFob.toFixed(2));
    
    var flete = parseFloat(element.find('.txtFleteDetalle').val());
    var seguro = parseFloat(element.find('.txtSeguroDetalle').val());

    var cif = (nuevoSubTotalFob + flete + seguro);
    element.find('.txtciftotal').val(cif.toFixed(2));
    
    var advaloremp = parseFloat(element.find('.txtadvalorporcentaje').val());
    var advaloremv=(cif*advaloremp)/100;
    element.find('.txtadvaloremvalor').val(advaloremv.toFixed(2));

    var sada = parseFloat(element.find('.txtSada').val());
    var sscto = parseFloat(element.find('.txtSscto').val());
    var fargo = parseFloat(element.find('.txtFargo').val());
    var vobo = parseFloat(element.find('.txtVBDetalle').val());
    var devctndr = parseFloat(element.find('.txtDevctndr').val());
    var fleteinterno = parseFloat(element.find('.txtFleteInternoDetalle').val());
    var agenteaduanas = parseFloat(element.find('.txtAgenteAduanaDetalle').val());
    var goemp = parseFloat(element.find('.txtGoemp').val());    
    var costototal = cif + sada + sscto + fargo + vobo + devctndr + fleteinterno + agenteaduanas + advaloremv + goemp;
    
    element.find('.txtTotalDetalle').val(costototal.toFixed(2));
    
    var cifventas = fobunitario*1.3;
    element.find('.txtTotalUnitarioDetalle').val(cifventas.toFixed(2));   
    var cifTotalOC = totalConcepto('.txtciftotal');
    $('#txtTotalCifOC').val(cifTotalOC.toFixed(2));
    var totalimporte = totalConcepto('.txtTotalDetalle');
    $('#txtTotalimportevalorizadoocOC').val(totalimporte.toFixed(2));
    $('#txtTotalimportevalorizadoocOCA').val(totalimporte.toFixed(2));
    var Tc = parseFloat($('#txtTipoCambioGrabado').val());    
    $('#txtTotalimportevalorizadoocOCSoles').val((totalimporte*Tc).toFixed(2));
    console.log(costototal);
    console.log(cif);
    var porcDetalle = (costototal/cif) - 1;
    porcDetalle = porcDetalle.toFixed(2) * 100;
    element.find('.txtPorcentajeDetalle').val(porcDetalle);
}

function totalConcepto(concepto) {
    var valorconcepto = 0.00;
    var TotalConceptoOC = 0.00;
    $(concepto).each(function () {
        valorconcepto = parseFloat($(this).val().replace(',', ''));
        TotalConceptoOC += parseFloat(valorconcepto.toFixed(2));
    });
    return TotalConceptoOC;
}

function calcularAdvaloren() {
    valorconcepto = 0;
    valorPorcentaje = 0;
    valorTotalAdvaloren = $('#txtTotaladvaloremOC').val().replace(',', '');
    //console.log(valorTotalAdvaloren);
    var cont = 0;
    $('.txtAdvaloremPDetalle').each(function () {
        padre = $(this).parents('tr');
        valorPorcentaje = $('.txtAdvaloremPDetalle' + ':eq(' + cont + ')').val();
        //console.log(valorPorcentaje);
        valorconcepto = ((valorTotalAdvaloren * valorPorcentaje) / 100);
        console.log(valorconcepto);
        $('.txtAdvaloremVDetalle:eq(' + cont + ')').val(valorconcepto.toFixed(2));
        cont++;
    });
}

function activarConcepto(concepto) {
    $(concepto).each(function () {
        //$(this).removeAttr('disabled');
        $(this).removeAttr('readonly');
    });
    var item = $(this).parents('tr');
    //recalcular(item);
}

function desactivarConcepto(concepto) {
    $(concepto).each(function () {
        //$(this).attr('disabled','disabled');
        $(this).attr('readonly', 'readonly').val(0);
    });
    var item = $(this).parents('tr');
    //recalcularxFila(item);
}

function modoDesactivado() {
    desactivarConcepto('.txtGateInDetalle');
    desactivarConcepto('.txtVBDetalle');
    desactivarConcepto('.txtFlatDetalle');
    desactivarConcepto('.txtBoxFeeDetalle');
    desactivarConcepto('.txtInsuranceFeeDetalle');
    desactivarConcepto('.txtSobreestadiaDetalle');
    desactivarConcepto('.txtDocFeeDetalle');
    desactivarConcepto('.txtGasAdmDetalle');
    desactivarConcepto('.txtAgenteAduanaDetalle');
}

function desactivaAdicionales() {
    //desactivarConcepto('.txtCF1Detalle');
    //desactivarConcepto('.txtCF2Detalle');
    desactivarConcepto('#txtTotalcostocv1OC');
    desactivarConcepto('#txtTotalcostocv2OC');
    desactivarConcepto('#txtTotalcostocv3OC');
}

function calcularConceptos() {
    var totalConceptoPorcentaje = totalConcepto('.txtCarton');
    calcularParteConceptos('.txtCarton', '.txtFleteDetalle', $('#txtFleteOC').val(), totalConceptoPorcentaje);
    recalcularFobDetalle();
    calcularParteConceptos('.txtfobTotalDetalle', '.txtSeguroDetalle', $('#txtSeguroOC').val(), $('#txtFobTotalOC').val());
    recalcularFobDetalle();
    calcularParteConceptos('.txtfobTotalDetalle', '.txtSada', $('#txtTotalSada').val(), $('#txtFobTotalOC').val());
    recalcularFobDetalle();
    calcularParteConceptos('.txtfobTotalDetalle', '.txtSscto', $('#txtTotalSscto').val(), $('#txtFobTotalOC').val());
    recalcularFobDetalle();
    calcularParteConceptos('.txtfobTotalDetalle', '.txtFargo', $('#txtTotalFargo').val(), $('#txtFobTotalOC').val());
    recalcularFobDetalle();
    calcularParteConceptos('.txtfobTotalDetalle', '.txtVBDetalle', $('#txtTotalVoBo').val(), $('#txtFobTotalOC').val());
    recalcularFobDetalle();
    calcularParteConceptos('.txtfobTotalDetalle', '.txtDevctndr', $('#txtTotalDevctndr').val(), $('#txtFobTotalOC').val());
    recalcularFobDetalle();
    calcularParteConceptos('.txtCarton', '.txtFleteInternoDetalle', $('#txtTotalFleteinterno').val(), totalConceptoPorcentaje);
    recalcularFobDetalle();
    calcularParteConceptos('.txtciftotal', '.txtAgenteAduanaDetalle', $('#txtTotalAgenteaduanas').val(), totalConceptoPorcentaje);
    recalcularFobDetalle();
}