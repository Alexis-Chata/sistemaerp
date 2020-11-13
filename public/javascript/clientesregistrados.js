$(document).ready(function () {
    var lstZona = $('#lstZona').html();
    var lstRegionCobranza = $('#lstRegionCobranza').html();
    var lstProvincia = $('#lstProvincia').html();
    var lstDistrito = $('#lstDistrito').html();

    /**************  Listas ***********/
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
        //console.log(idzona);
        if (idzona == "") {
            $('#lstZona').html(lstZona);
        } else {
            cargaZonas(idzona);
        }
    });

    $('#txtVendedor').autocomplete({
        source: "/vendedor/autocompletevendedor/",
        select: function (event, ui) {
            $('#idVendedor').val(ui.item.id);
        }
    });
    
    $('#btnConsultarExcel').click(function () {
        if ($('#txtFechaInicio').val().length == 0) {
            $('#txtFechaInicio').focus();
            return false;
        } else if ($('#txtFechaFin').val().length == 0) {
            $('#txtFechaFin').focus();
            return false;
        } else {
            $('#frmRegistrados').attr('action', '/excel/clientesregistrados');
            return true;
        }
    });

});


function cargaRegionCobranza(idpadre) {
    $.ajax({
        url: '/zona/listaCategoriaxPadre',
        type: 'post',
        async: false,
        dataType: 'html',
        data: {'idpadrec': idpadre},
        success: function (resp) {
            //console.log(resp);
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