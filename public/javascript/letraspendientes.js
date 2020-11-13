$(document).on('ready', function () {
    $('#tblnumeroletra').on('click','.checkcobro', function (){
        var id = $(this).data('id');
        var estado = 0;
        if ($(this).prop('checked')) {
            estado = 1;
        }
        $.ajax({               
            url:'/ordencobro/cargardetalleordencobro/',
            type:'post',
            datatype:'json',
            data:{'iddetalle': id, 'estado': estado},
            success:function(datos){
                if(datos['rspta'] == true) {
                    alert('Se Cargo Correctamente!');
                } else {
                    alert('Hubo Problemas en la Base de Datos.');
                }
            },
            error:function(error){
                    console.log('error');
            }
	});
    });
    
    $('#fom-listaletras').submit(function (){
        if($('#talista').val().length == 0) {
            return false;
        }
        return true;
    });
    
    $('.checkcobro').click(function (){
        var id = $(this).data('id');
        var estado = 0;
        if ($(this).prop('checked')) {
            estado = 1;
        }
        $.ajax({               
            url:'/ordencobro/cargardetalleordencobro/',
            type:'post',
            datatype:'json',
            data:{'iddetalle': id, 'estado': estado},
            success:function(datos){
                if(datos['rspta'] == true) {
                    alert('Se Cargo Correctamente!');
                } else {
                    alert('Hubo Problemas en la Base de Datos.');
                }
            },
            error:function(error){
                    console.log('error');
            }
	});
    });
    
    $('#idnroletra').autocomplete({
        source: "/letras/listarletraspendientesAC/",
        select: function(event, ui){
            var iddetalle=ui.item.id;
            MostrarLetra(iddetalle);
        }
    });
    
});

function MostrarLetra(iddetalle) {
    $.ajax({
        url: '/letras/verLetraPendiente',
        type: 'post',
        datatype: 'html',
        data: {'iddetalle': iddetalle},
        success: function (resp) {
            $('#tblnumeroletra').html(resp);
            $('#idnroletra').val('');
        }
    });
}