
$(document).ready(function(){
    var click=0;

    $('#btnCancelar').click(function(e){
        e.preventDefault();
        window.location="/empaque/listar";

    });
    
    $('#btnGrabar').click(function(e){
        /*if (click==1) {
            e.preventDefault();
            alert('La peticion ya fue enviada');
        }
        click++;*/
    });
});