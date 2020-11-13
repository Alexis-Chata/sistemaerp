$(document).ready(function () {
    
    $('#txtusuario1').autocomplete({
        source: "/cliente/buscarClienteUnificar/",
        select: function(event, ui){
            var id = ui.item.id;
            if(id != $('#clientedestino').val()){
                $('#clienteorigen').val(id);
                $.ajax({
                    url: "/cliente/DxClienteUnificar/",
                    type: "post",
                    data: {txtid: id},
                    success: function (data) {
                        $('#usuario1').html(data);
                        $('#txtusuario1').val('');
                        $('#txtusuario2').focus();
                    }
                });
            } else {
                $('#usuario1').html("<center><b><h1 style='color: #990000;'>EL USUARIO ORIGEN NO PUEDE SER EL MISMO QUE EL USUARIO DESTINO</h1></b></center>");
                $('#txtusuario1').focus();
                $('#clienteorigen').val('');
            }
        }
    });
    
    $('#txtusuario2').autocomplete({
        source: "/cliente/buscarClienteUnificar/",
        select: function(event, ui){
            var id = ui.item.id;
            if(id != $('#clienteorigen').val()){
                $('#clientedestino').val(id);
                $.ajax({
                    url: "/cliente/DxClienteUnificar/",
                    type: "post",
                    data: {txtid: id},
                    success: function (data) {
                        $('#usuario2').html(data);
                        $('#txtusuario2').val('');
                    }
                });
            } else {
                $('#usuario2').html("<center><b><h1 style='color: #990000;'>EL USUARIO DESTINO NO PUEDE SER EL MISMO QUE EL USUARIO ORIGEN</h1></b></center>");
                $('#txtusuario2').focus();
                $('#clientedestino').val('');
            }
        }
    });
    
    $('#form-unificar').submit(function (){
        if($('#clienteorigen').val().length == 0) {
            $('#txtusuario1').focus();
        } else if($('#clientedestino').val().length == 0) {
            $('#txtusuario2').focus();
        } else if($('#clienteorigen').val() == $('#clientedestino').val()) {
            $('#usuario2').html("<center><b><h1 style='color: #990000;'>EL USUARIO DESTINO NO PUEDE SER EL MISMO QUE EL USUARIO ORIGEN</h1></b></center>");
            $('#txtusuario2').focus();
            $('#clientedestino').val('');
        } else {
            return true;
        }
        return false;
    });
    
});

