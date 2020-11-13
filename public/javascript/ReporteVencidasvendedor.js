$(document).on('ready', function () {
          

    $("#groupVencimiento").addClass("ocultar");
    $("#groupLetras").addClass("ocultar");
    $('#btnResumenDetallado').hide();
    
    $('#lstRecepcionLetras').change(function () {
        if ($(this).val() == 4) {
            $('#btnResumenDetallado').show();
        } else {
            $('#btnResumenDetallado').hide();
        }
    });
    
    $('#cmbCondicion').change(function () {
        if ($(this).val() == 1) {
            if ($('#lstRecepcionLetras').val() == 4) {
                $('#btnResumenDetallado').show();
            } else {
                $('#btnResumenDetallado').hide();
            }
        } else {
            $('#btnResumenDetallado').hide();
        }
    });
    
    $('#btnPDF').click(function () {
        $('#frmLetras').attr('action', '/pdf/resumencobranzas');
        $('#frmLetras').submit();
    });

    $('#btnExcel').click(function () {
        if ($('#cmbCondicion').val() == 1) {
            $('#frmLetras').attr('action', '/excel/reporteventasvencidosvendedor_letras');
            $('#frmLetras').submit();
        } else if ($('#cmbCondicion').val() == 2) {
            $('#frmLetras').attr('action', '/excel/reporteventasvencidosvendedor_creditos');
            $('#frmLetras').submit();
        } else if ($('#cmbCondicion').val() == 3) {
            $('#frmLetras').attr('action', '/excel/reporteventasvencidosvendedor_contado');
            $('#frmLetras').submit();
        } else {
            alert("Especifique Detalle...");
        } 
        $('#cmbCondicion').focus();
        return false;               
    });
    

    
    $('#btnAnalisis').click(function () {
        $('#frmLetras').attr('action', '/pdf/analisiscobranzageneral');
        $('#frmLetras').submit();
        return false;
    });

    $('#cmbCondicion').on("change", function () {
        valor = $(this).attr("value");
        if (valor == 0) {
            $("#groupLetras").addClass("ocultar");
            $("#groupVencimiento").addClass("ocultar");
        }
        if (valor == 1) {
            $("#groupLetras").removeClass("ocultar");
            $("#groupVencimiento").addClass("ocultar");
        }
        if (valor == 2) {
            $("#groupLetras").addClass("ocultar");
            $("#groupVencimiento").removeClass("ocultar");
            $("#cmbCondVencimiento2").addClass("ocultar");
            $("#cmbCondVencimiento3").addClass("ocultar");

        }
        if (valor == 3) {
//            $("#groupLetras").addClass("ocultar");
//            $("#groupVencimiento").addClass("ocultar");
//            $("#groupCelestium").removeClass("ocultar");
//       
//       
//            $(".cls_power").addClass("ocultar");

            $("#groupLetras").removeClass("ocultar");
            $("#groupVencimiento").addClass("ocultar");
        }
//        if(valor!=3){
//         $(".cls_power").removeClass("ocultar");
//        }
    });
    $('#cmbCondVencimiento1').on("change", function () {
        valor = $(this).attr("value");
        if (valor == 1) {
            $("#cmbCondVencimiento2").removeClass("ocultar");
            $("#cmbCondVencimiento3").addClass("ocultar");
       }

        if (valor == 2) {
            $("#cmbCondVencimiento2").addClass("ocultar");
            $("#cmbCondVencimiento3").removeClass("ocultar");
       }

    });

    $("#chk1").on("click",function(){
        if ($('#chk1').is(':checked') ) {
           $("#chk2").removeAttr('checked');   
        }
    });
    $("#chk2").on("click",function(){    
        if ($('#chk2').is(':checked') ) {
           $("#chk1").removeAttr('checked');   
        }      
    });
    $('#mostrarPowerAcoustik').addClass('ocultar');            
//    $('#cmbCondicion').change(function(){
//        if($(this).attr("value")==3){
//           $('#mostrarPowerAcoustik').removeClass('ocultar');            
//        }else{
//           $('#mostrarPowerAcoustik').addClass('ocultar');             
//        }
//    });
     $("#chk10").on("click",function(){
        if ($('#chk10').is(':checked') ) {
           $("#chk20").removeAttr('checked');   
        }
    });
    $("#chk20").on("click",function(){    
        if ($('#chk20').is(':checked') ) {
           $("#chk10").removeAttr('checked');   
        }      
    });

    $('#btnResumenDetallado').click(function () {
        $('#frmLetras').attr('action', '/excel/resumendetalladodeletras');
        $('#frmLetras').submit();
        return false;
    });
    $(".estadoVencida").on("click change",function(){    
       $("#txtFechaInicio,#txtFechaFinal").attr("value","");
    });
    $("#txtFechaInicio,#txtFechaFinal").on("click change",function(){    
        $('.estadoVencida option:eq(0)').prop('selected', true)
    });

    
    
});