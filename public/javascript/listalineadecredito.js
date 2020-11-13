$(document).ready(function () {
    
    var lstZona=$('#lstZona').html();
    var lstRegionCobranza=$('#lstRegionCobranza').html();
        
    $('#lstCategoriaPrincipal').change(function () {
        idpadre = $(this).val();
        if (idpadre == "") {
            $('#lstRegionCobranza').html(lstRegionCobranza);
            $('#lstRegionCobranza').change();
        } else {
            cargaRegionCobranza(idpadre);
        }
        $('#lstRegionCobranza').change();
    });

    $('#lstRegionCobranza').change(function () {
        idzona = $(this).val();
        console.log(idzona);
        if (idzona == "") {
            $('#lstZona').html(lstZona);
        } else {
            cargaZonas(idzona);
        }
    });
    
    $('#txtCliente').autocomplete({
        source: "/cliente/autocomplete2/",
        select: function(event, ui){
            $('#idCliente').val(ui.item.id);

    }});
});

function cargaRegionCobranza(idpadre) {
    $.ajax({
        url: '/zona/listaCategoriaxPadre',
        type: 'post',
        async: false,
        dataType: 'html',
        data: {'idpadrec': idpadre},
        success: function (resp) {
            $('#lstRegionCobranza').html(resp);
        }
    });
}

function cargaZonas(idzona) {
    $.ajax({
        url: '/zona/listaZonasxCategoria',
        type: 'post',
        async: false,
        dataType: 'html',
        data: {'idzona': idzona},
        success: function (resp) {
            $('#lstZona').html(resp);
        }
    });
}