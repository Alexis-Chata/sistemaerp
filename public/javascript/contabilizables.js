$(document).on('ready', function(){
    $('#liSubLinea').hide();
    $('#liProducto').hide();
    $('#mostrarPDF').hide();
    msboxTitle = "Reporte de Stock de Producto";
    $('input[name="rbFiltro"]').change(function(){
        $('#lstLinea option').eq(0).attr('selected','selected');
	$('#lstSubLinea option').eq(0).attr('selected','selected');
        $('#txtCodigoProducto').val('');
        $('#txtIdProducto').val('');
        if(this.value == "3"){  
                $('#liLinea').show();
                $('#liSubLinea').hide();
                $('#liProducto').hide();
        }else if(this.value == "4"){
                $('#liLinea').show();
                $('#liSubLinea').show();
                $('#liProducto').hide();
        }else{
                $('#liAlmacen').hide();
                $('#liLinea').hide();
                $('#liSubLinea').hide();
                $('#liProducto').show();
                $('#txtCodigoProducto').focus();
        }
    });
    
    $('#opcFormato').change(function (){
        if ($(this).val() == '2') {
            $('#tabladinamica').attr('style', 'display: none');
            $('#ContenidoCuadro').removeAttr('style');
        } else {
            $('#ContenidoCuadro').attr('style', 'display: none');
            $('#tabladinamica').removeAttr('style');
        }
    });
    
    $('#lstLinea').change(function(){
        cargaSubLinea();
    });
        
    $('#btnConsultar').click(function (e){
        e.preventDefault();
        consultarDureza(msboxTitle);
    });
    
});

function cargaSubLinea(){
    idLinea = $('#lstLinea option:selected').val();
    if(idLinea){
            ruta = "/sublinea/listaroptions/" + idLinea;
            $.post(ruta, function(data){
                    $('#lstSubLinea').html('<option value="">-- Sub Linea --' + data);
            });
    }else{
            $('#lstSubLinea').html('<option value="">-- Sub Linea --');
    }
}

function consultarDureza(msboxTitle) {
    idLinea = $('#lstLinea option:selected').val();
    idSubLinea = $('#lstSubLinea option:selected').val();
    idProducto = $('#txtIdProducto').val();
    filtro = $('input[name="rbFiltro"]:checked').val();

    mensaje = "";
    if(filtro == "3"){
        if(idLinea == ""){
            mensaje = "Seleccione correctamente la Linea";
        }
    }else if(filtro == "4"){
        if(idLinea == "" || idSubLinea == ""){
            mensaje = "Seleccione correctamente la Linea y Sublinea";
        }
    }else if(filtro == "5"){
        if(idProducto == ""){
            mensaje = "Ingrese correctamente el nombre del producto";
        }
    }else{
        mensaje = "";
    }
    if(mensaje!=""){
        $.msgbox(msboxTitle, mensaje);
        execute();
    }else{
        $('#btnConsultar').attr('disabled','disabled');
        $('#idAlmacen').html('<img src="/imagenes/cargando.gif" width="30">');
        $('#idFecha').html('<img src="/imagenes/cargando.gif" width="30">');
        ruta = "/producto/contabilizables/";
        formato = $('#opcFormato').val();
        $.post(ruta, {txtidLinea: idLinea, txtidSubLinea: idSubLinea, txtidproducto: idProducto}, function(data){
            
            if (formato == 1) $("#dataGridReport").data("kendoGrid").dataSource.data(data);
            else $('#ContenidoTabla').html(data);
            
            $('#idAlmacen').html("CORPORACION POWER ACOUSTIK S.A.C.");
            $('#idFecha').html($('#txtFechaInicio').val());
            
           
            $('#btnConsultar').removeAttr('disabled');

            $('#txtCodigoProducto').val('');
            $('#txtIdProducto').val('');

            $('#idLinea').val(idLinea);
            $('#idSubLinea').val(idSubLinea);
            $('#idProducto').val(idProducto);
            $('#fecha').val($('#txtFechaInicio').val());
        
            $('#mostrarPDF').show();
        });
    }
}


