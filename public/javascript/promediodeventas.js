$(document).on('ready', function(){
    $('#liSubLinea').hide();
    $('#liProducto').hide();
    $('#mostrarPDF').hide();
    $('input[name="rbFiltro"]').change(function(){
        $('#lstLinea').val("");
	$('#lstSubLinea').val("");
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
                $('#liLinea').hide();
                $('#liSubLinea').hide();
                $('#liProducto').show();
        }
    });
    
    $('#lstLinea').change(function(){
        cargaSubLinea();
    });
    
    $('#btnConsultar').click(function (e){
        e.preventDefault();
        consultarPromedio();
    });
    
});

function cargaSubLinea(){
    idLinea = $('#lstLinea').val();
    if(idLinea){
            ruta = "/sublinea/listaroptions/" + idLinea;
            $.post(ruta, function(data){
                    $('#lstSubLinea').html('<option value="">-- Sub Linea --' + data);
            });
    }else{
            $('#lstSubLinea').html('<option value="">-- Sub Linea --');
    }
}

function consultarPromedio() {
    var idLinea = $('#lstLinea').val();
    var idSubLinea = $('#lstSubLinea').val();
    var idProducto = $('#txtIdProducto').val();
    var filtro = $('input[name="rbFiltro"]:checked').val();
    var mensaje = "";
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
        $.msgbox("Mensaje ", mensaje);
        execute();
    }else{
        $('#mostrarPDF').hide();
        $('#btnConsultar').attr('disabled','disabled');
        $('#contenido').html('<center><img src="/imagenes/cargando.gif" width="100"></center>');
        $.post("/reporte/cantidadventasxmes/", {txtidLinea: idLinea, txtidSubLinea: idSubLinea, txtidproducto: idProducto, filtro: filtro}, function(data){
            $('#contenido').html(data);
            $('#mostrarPDF').show();
            $('#btnConsultar').removeAttr('disabled');
            
            $('#txtCodigoProducto').val('');
            $('#txtIdProducto').val('');

            $('#idLinea').val(idLinea);
            $('#idSubLinea').val(idSubLinea);
            $('#idProducto').val(idProducto);
        });
    }
}