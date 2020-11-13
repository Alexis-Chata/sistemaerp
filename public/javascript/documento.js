$(document).on('ready',function(){

    $('#btnCancelar').on('click',function(e){
        e.preventDefault();
        window.location='/documento/listaDocumentos/';
    });

    $("#seleccion").change(function(){
        var id=$("#seleccion option:selected").text();
        var url='/documento/listaDocumentos/'+id;
        window.location=url;
    });
        
        $('#btnConsultarDocumentoRegistrado').click(function () {
            
            $('#documentosRegistradosHtml').html("<center><img src='/public/imagenes/cargando.gif' width='300'></center>");
            $.ajax({
                url: "/documento/consultaDocumentoRegistrado",
                data: {'txtFecha': $('#txtFecha').val(),'idTipoDocumento':$('#idDocumento').val()},
                type: "POST",
                success: function (datos) {
                    $('#documentosRegistradosHtml').html(datos);
                    $('#btnConsultarDocumentoRegistrado').removeAttr('disabled');
                    
                },
                error: function (a, b, c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                }
            });
        });
});