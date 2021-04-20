$(document).ready(function () {
    
    checkarAdicionales();
    
    $('#idFAdLL').change(function () {
        $('#obsAdicional').show();
        $('#obsAdicional > input').focus();
    });

    if ($('#vbimportaciones').val()==1) {
        
        $('#idCifPorcentajeCPA').attr('disabled','disabled');
		$('input').attr('disabled','disabled');
		$('select').attr('disabled','disabled');
		
		$('#btnRegistrarOrden').css('display','none');
	}

    $('#btnRegistrarOrden').click(function (e) {
        if ($('#conformidad').attr('checked') == "checked") {
            if (!confirm('¿Esta seguro de confirmar una vez grabado no va poder modificar la valorización?')) {
                e.preventDefault();
            }
        }
    });
    
    $('.txtCantidadDetalle, .txtfobDetalle').blur(function () {
        /*
        clase = $(this).attr('class');
        if (clase == 'txtVolumen numeric required') {
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
    });
    
    $('#idCifPorcentajeCPA').change(function () {
        if ($(this).val() == '') {
            $(this).val('30');
        } else if (parseFloat($(this).val()) <= 0 || parseFloat($(this).val()) > 100) {
            $(this).val('30');
        }
        recalcularFobDetalle();
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
    
    $('#chkScto').change(function () {
        if ($(this).attr('checked') == 'checked') {
            activarConcepto('#txtTotalSscto');
            $('#txtTotalSscto').focus();
        } else {
            for (var i = 1; i <= 2; i++) {
                $('.txtSscto').val(0);
                desactivarConcepto('#txtTotalSscto');
                recalcularFobDetalle();
            }
        }
    });
    
    $('#cmbAdicionales').change(function () {
        if ($(this).val() == '') {
            for (var i = 1; i <= 2; i++) {
                $('.txtSada').val(0);
                desactivarConcepto('#txtTotalSada');
                recalcularFobDetalle();
                $('.txtFargo').val(0);
                desactivarConcepto('#txtTotalFargo');
                recalcularFobDetalle();
                //$('#blockSCTO').hide();
                //$('.txtSscto').val(0);
                desactivarConcepto('#txtTotalSscto');
                recalcularFobDetalle();
            }
        } else {
            if ($(this).val() == 1) {                
                $('.txtFargo').val(0);
                desactivarConcepto('#txtTotalFargo');
                recalcularFobDetalle();
                activarConcepto('#txtTotalSada');
                $('#txtTotalSada').focus();/*
                if ($('#txtTotalSscto').val() > 0) {            
                    $('#chkScto').prop('checked', true);
                    activarConcepto('#txtTotalSscto');
                } else {
                    $('#chkScto').prop('checked', false);
                    desactivarConcepto('#txtTotalSscto');
                }*/
                //$('#blockSCTO').show();
            } else if ($(this).val() == 2) {
                $('.txtSada').val(0);
                desactivarConcepto('#txtTotalSada');
                recalcularFobDetalle();
                activarConcepto('#txtTotalFargo');
                $('#txtTotalFargo').focus();
                //$('#blockSCTO').hide();
                //$('.txtSscto').val(0);
                desactivarConcepto('#txtTotalSscto');
                recalcularFobDetalle();
            }
        }        
    });
     
    $('#btnCancelar').click(function (e) {
        e.preventDefault();
        window.location = "/importaciones/ordencompra";
    });

    $('#btnExcel').click(function (e) {
        e.preventDefault();
        id = $('input:hidden[name=idOrdenCompra]').val();
        window.location = "/importaciones/exportarordencompranuevo/" + id;
    });
    
    $('#PrintTodo').on('click', function (e) {
        e.preventDefault();
        $('#btnRegistrarOrden').hide();
        $('#PrintTodo').hide();
        $('#btnCancelar').hide();
        $('#btnExcel').hide();
        imprSelec('PrintdetalleOrdenCompra');
        $('#btnRegistrarOrden').show();
        $('#PrintTodo').show();
        $('#btnCancelar').show();
        $('#btnExcel').show();
    });

});

function checkarAdicionales () {       
    if ($('#txtTotalSada').val() > 0) {
        $('#cmbAdicionales').val(1);/*
        if ($('#txtTotalSscto').val() > 0) {            
            $('#chkScto').prop('checked', true);
        }*/
    }
    if ($('#txtTotalFargo').val() > 2) {
        //$('#blockSCTO').hide();
        $('#cmbAdicionales').val(2);
    }
}

function recalcularFobDetalle() {
    var porcetajetotal = 0;
    var nro = 0;
    $('.txtfobDetalle').each(function () {
        element = $(this).parents('tr');
        recalcularxFila(element);
        porcetajetotal = porcetajetotal + parseFloat(element.find('.txtPorcentajeDetalle').val());
        nro ++;
    });
    if (nro > 0) {
        porcetajetotal = porcetajetotal/nro;
    }
    $('#txtTotalpromedioporc').val(porcetajetotal.toFixed(2));
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
    if ($('#idCifPorcentajeCPA').val() == '') {
        $('#idCifPorcentajeCPA').val('30');
    } else if (parseFloat($('#idCifPorcentajeCPA').val()) <= 0 || parseFloat($('#idCifPorcentajeCPA').val()) > 100) {
        $('#idCifPorcentajeCPA').val('30');
    }
    var CifPorcentajeCPA = (parseFloat($('#idCifPorcentajeCPA').val())/100) + 1;
    var cifventas = fobunitario*CifPorcentajeCPA;
    element.find('.txtTotalUnitarioDetalle').val(cifventas.toFixed(2));   
    var cifTotalOC = totalConcepto('.txtciftotal');
    $('#txtTotalCifOC').val(cifTotalOC.toFixed(2));
    var totalimporte = totalConcepto('.txtTotalDetalle');
    $('#txtTotalimportevalorizadoocOC').val(totalimporte.toFixed(2));
    $('#txtTotalimportevalorizadoocOCA').val(totalimporte.toFixed(2));
    var Tc = parseFloat($('#txtTipoCambio').val());    
    $('#txtTotalimportevalorizadoocOCSoles').val((totalimporte*Tc).toFixed(2));
    console.log(costototal);
    console.log(cif);
    var porcDetalle = (costototal/cif) - 1;
    porcDetalle = porcDetalle.toFixed(2) * 100;
    element.find('.txtPorcentajeDetalle').val(porcDetalle);
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

function totalConcepto(concepto) {
    var valorconcepto = 0.00;
    var TotalConceptoOC = 0.00;
    $(concepto).each(function () {
        valorconcepto = parseFloat($(this).val().replace(',', ''));
        TotalConceptoOC += parseFloat(valorconcepto.toFixed(2));
    });
    return TotalConceptoOC;
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

function recalcularDetalles() {
    $('.txtfobDetalle').each(function () {
        element = $(this).parents('tr');
        recalcularxFila(element);
    });
}