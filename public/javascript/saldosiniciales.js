$(document).ready(function(){
    respuesta=$('#txtRespuesta').attr("value");
    $('#txtProductoInventario').autocomplete({
        source: "/producto/buscarAutocompleteLimpio/",
        select: function(event, ui){
            $('#txtIdProducto').val(ui.item.id);
            //$('#codigoProducto').html(ui.item.value);
            $('#txtDescripcion').val(ui.item.tituloProducto);
     prueba(ui.item.id);

        }
        
    });

    function prueba(producto){
       
            $.ajax({
                type: 'get',
                url: '/inventario/verificaExistenciaSaldoInicial/',
                data: 'txtIdProducto='+producto,
                dataType: "json",
                success: function (json) {
                    if (json.variable =="true") {
                            $('#frmSaldosIniciales').attr('action','/inventario/saldosiniciales?producto');
                            $('#frmSaldosIniciales').submit();   
                    }
                }
            });    

    }
    if(respuesta==1){
        alert("Guardo Correctamente");
    };
    if(respuesta==2){
        alert("EL producto ya fue registrado");
    };
    $("#seleccion").change(function(){
        var id=$("#seleccion option:selected").text();
        var url='/inventario/saldosiniciales/'+id;
        window.location=url;
    });
    
    $('#btnGrabar').click(function () {
        if($('#txtIdProducto').val() == "" || $('#txtDescripcion').val() == "" || $('#txtCantidad1').val() == "" || $('#txtCantidad2').val() == "" || $('#txtCunitario').val() == ""){
            alert("Complete los campos");
        }else {
            $('#frmSaldosIniciales').attr('action','/inventario/grabarSaldoInicial');
            $('#frmSaldosIniciales').submit();
        } 
        return false;               
    });
    
    $('#btnBuscar').click(function () {
         if($('#txtIdProducto').val() == ""){
            alert("Complete los campos");
        }else {
            $('#frmSaldosIniciales').attr('action','/inventario/saldosiniciales?producto');
            $('#frmSaldosIniciales').submit();
        } 
        return false;
    });
    $('#chkStock').click(function () {
            $('#frmSaldosIniciales').attr('action','/inventario/saldosiniciales?stock');
            $('#frmSaldosIniciales').submit();
    });
    
    //Mostrar el datos del saldo inicial
    $('.btnEditarSaldoInicial').click(function(e){
        e.preventDefault();
        $('#tblSaldosIniciales tr').removeClass();
        $(this).parents('tr').addClass('active-row');
        var ruta = $(this).attr('href');
        mostrarSaldoInicial(ruta);
    });
    
    //Mostrar datos del saldo inicial
    function mostrarSaldoInicial(ruta){
        $.post(ruta, function(data){
            $('#tblDetalleSaldoInicial tbody').html(data).parent().show();
        });
    }
    $(".btnActualizar").live("click",function(){
       padre=$(this).parents('tr');
       idsaldo=padre.find('.txtIdSaldo').val()
       stock=padre.find('.txtUpstock').val()
       cunitario=padre.find('.txtUpCunitario').val()
       tcambio=padre.find('.txtUpTCambio').val()
       
       $.ajax({
            type: 'get',
            url: '/inventario/actualizarSaldoInicial/',
            data: 'idsaldo='+idsaldo+'&cantidad1='+stock+'&costounitario='+cunitario+'&tcambio='+tcambio,
            dataType: "json",
            success: function (json) {
                if (json.variable == "false") {
                    alert("Error al Actualizar, intentelo otra vez");   
                    location.href='';
                } else {
                    alert("Actualizado Correctamente");
                    location.href='/inventario/saldosiniciales/';
                }
            }, //
            error: function () {
                alert('El servidor no responde');
            }
        });
    });
});