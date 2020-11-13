$(document).on('ready', function () {
          
    $('#mostrarPowerAcoustik').addClass('ocultar');     
    $("#groupVencimiento").addClass("ocultar");
    $("#groupLetras").addClass("ocultar");
    $('#btnResumenDetallado').hide();
    $('.blockIncobrables').addClass("ocultar");
    $('#btnExcelFormato2').hide();
    
    $('#lstCategoriaPrincipal').change(function () {
        $('#btnResumenDetallado').hide();
        $('#btnExcelFormato2').hide();
        $('#btnExcelContadoFormato2').hide();
        $('#idcmbCondicionIncobrable').val(0);
        $('#cmbCondicion').val(0);
        if($(this).val() == 39 || $(this).val() == 40 || $(this).val() == 48) {
            $('.blockIncobrables').removeClass("ocultar");
            $('.blockCobrables').addClass("ocultar");
        } else {
            $('.blockIncobrables').addClass("ocultar");
            $('.blockCobrables').removeClass('ocultar');
            evaluarFiltros(1);
        }
    });
    
    $('#lstRecepcionLetras').change(function () {
        evaluarFiltros(11);
    });
    $('#idcmbCondicionIncobrable').change(function () {
        evaluarFiltros(111);
    });
    
    $('#cmbCondicion').change(function () {
        evaluarFiltros(1);
    });
    
    $('#btnPDF').click(function () {
        $('#frmLetras').attr('action', '/pdf/resumencobranzas');
        $('#frmLetras').submit();
    });

    $('#btnExcel').click(function () {
        if ($('#cmbCondicion').val() == 1) {
            $('#frmLetras').attr('action', '/excel/detalladodeletras');
            $('#frmLetras').submit();
        } else if ($('#cmbCondicion').val() == 2) {
            $('#frmLetras').attr('action', '/excel/detalladoCreditos');
            $('#frmLetras').submit();
        } else if ($('#cmbCondicion').val() == 3) {
            if ($('#chk10').is(':checked') ) {
              $('#frmLetras').attr('action', '/excel/detalladoEmpresa');
            }
            if ($('#chk20').is(':checked') ) {
              $('#frmLetras').attr('action', '/excel/reportePowerAcoustik');
            }
            $('#frmLetras').submit();
        } else if ($('#cmbCondicion').val() == 4) {
            $('#frmLetras').attr('action', '/excel/detalladoContadoFormato2');
            $('#frmLetras').submit();
        } else {
            alert("Especifique Detalle...");
        } 
        $('#cmbCondicion').focus();
        return false;               
    });
    
    $('#btnExcelFormato2').click(function () {
        
        if ($('#idcmbCondicionIncobrable').val() == 1) {
            $('#frmLetras').attr('action', '/excel/detalladodeletras');
            $('#frmLetras').submit();
        } else if ($('#idcmbCondicionIncobrable').val() == 2) {
            $('#frmLetras').attr('action', '/excel/detalladoCreditos');
            $('#frmLetras').submit();
        } else if ($('#idcmbCondicionIncobrable').val() == 3) {
            if ($('#chk10').is(':checked') ) {
              $('#frmLetras').attr('action', '/excel/detalladoEmpresa');
            }
            if ($('#chk20').is(':checked') ) {
              $('#frmLetras').attr('action', '/excel/reportePowerAcoustik');
            }
            $('#frmLetras').submit();
        } else {
            alert("Especifique Detalle...");
        } 
        $('#idcmbCondicionIncobrable').focus();
        return false;               
    });
    
    $('#btnExcelContadoFormato2').click(function () {
        
        if ($('#idcmbCondicionIncobrable').val() == 1) {
            $('#frmLetras').attr('action', '/excel/detalladoContadoFormato2');
            $('#frmLetras').submit();
        } else {
            alert("Especifique Detalle...");
        } 
        $('#idcmbCondicionIncobrable').focus();
        return false;               
    });
    
    $('#btnAnalisis').click(function () {
        $('#frmLetras').attr('action', '/pdf/analisiscobranzageneral');
        $('#frmLetras').submit();
        return false;
    });
    
    $('#cmbCondVencimiento1').on("change", function () {
        evaluarFiltros(22);
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
    
    $('#btnExcelIncobrable').click(function () {
        if($('#lstCategoriaPrincipal').val() == 39) { 
            $('#frmLetras').attr('action', '/excel2/detalleincobrable');
        } else if ($('#lstCategoriaPrincipal').val() == 40 || $('#lstCategoriaPrincipal').val() == 48) {
            $('#frmLetras').attr('action', '/excel2/detallepesados');
        }
        $('#frmLetras').submit();
        return false;
    });
    
});

function evaluarFiltros(ejecucion) {
    if (ejecucion == 1) {
        $('#mostrarPowerAcoustik').addClass('ocultar');
        $("#groupLetras").addClass("ocultar");
        $("#groupVencimiento").addClass("ocultar");
        $(".cls_power").removeClass("ocultar");
        var condicion = $('#cmbCondicion').val();
        $('#btnResumenDetallado').hide();
        if (condicion == 1) {
            $("#groupLetras").removeClass("ocultar");
            ejecucion = 11;
        } else if (condicion == 2) {
            $("#groupLetras").addClass("ocultar");
            $("#groupVencimiento").removeClass("ocultar");
            ejecucion = 22;
        }  else if (condicion == 3) {
            $('#mostrarPowerAcoustik').removeClass('ocultar');
            $(".cls_power").addClass("ocultar");
        }
    }
    if (ejecucion == 11) {
        if ($('#lstRecepcionLetras').val() == 4) {
            $('#btnResumenDetallado').show();
        } else {
            $('#btnResumenDetallado').hide();
        }
    }
    
    if (ejecucion == 22) {
        var valor = $('#cmbCondVencimiento1').val();
        $("#cmbCondVencimiento2").addClass("ocultar");
        $("#cmbCondVencimiento3").addClass("ocultar");
        if (valor == 1) {
            $("#cmbCondVencimiento2").removeClass("ocultar");
        }
        if (valor == 2) {
            $("#cmbCondVencimiento3").removeClass("ocultar");            
        }
    }
    if (ejecucion == 111) {
        $('#btnExcelFormato2').hide();
        $('#btnResumenDetallado').hide();
        $('#btnExcelContadoFormato2').hide();
        if($('#idcmbCondicionIncobrable').val() == 4) {
            $('#btnResumenDetallado').show();
        }
        if($('#idcmbCondicionIncobrable').val() == 2){
            $("#chk1").attr('checked', false);
            $("#chk2").attr('checked', true);
            $('#btnExcelFormato2').show();
        }
        if($('#idcmbCondicionIncobrable').val() == 1){
            $("#chk1").attr('checked', false);
            $("#chk2").attr('checked', true);
            $('#btnExcelContadoFormato2').show();
        }
    }
}
