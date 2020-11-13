$(document).ready(function () {

    $('#txtClientexIdCliente').autocomplete({
        source: "/cliente/autocomplete2/",
        select: function (event, ui) {
            $('#txtIdCliente').val(ui.item.id);
        }
    });

    $('#btnLimpiarCliente').click(function () {
        $('#txtClientexIdCliente').val('');
        $('#txtIdCliente').val('');
        $('#txtClientexIdCliente').focus();
        return false;
    });

    $('#lstCategoriaPrincipal').change(function () {
        listaZonaCobranza($(this).val());
    });

    $('#lstCategoria').change(function () {
        listaZonasxCategoria($(this).val());
    });
    
    $('#btnExcel').click(function () {
        lstCategoriaPrincipal = $("#lstCategoriaPrincipal option:selected").text();
        lstCategoria = $("#lstCategoria option:selected").text();
        lstZona = $("#lstZona option:selected").text();
        val_lstCategoriaPrincipal = $("#lstCategoriaPrincipal").attr("value");
        val_lstCategoria = $("#lstCategoria").attr("value");
        val_lstZona = $("#lstZona").attr("value");
        if (val_lstCategoriaPrincipal == "") {
            condiciones = "Todas las zonas";
        } else {
            condiciones = lstCategoriaPrincipal;
        }
        if (val_lstCategoria == "") {
            condiciones += "";
        } else {
            condiciones += " - " + lstCategoria;
        }
        if (val_lstZona == "") {
            condiciones += "";
        } else {
            condiciones += " - " + lstZona;
        }
        $("#txtCondiciones").attr("value", condiciones);
        $('#frmPendientesxvendedor').attr('action', '/excel2/pendientesporvendedor');
        $('#frmPendientesxvendedor').submit();
    });
    
    $('#btnPDF').click(function () {
        lstCategoriaPrincipal = $("#lstCategoriaPrincipal option:selected").text();
        lstCategoria = $("#lstCategoria option:selected").text();
        lstZona = $("#lstZona option:selected").text();
        val_lstCategoriaPrincipal = $("#lstCategoriaPrincipal").attr("value");
        val_lstCategoria = $("#lstCategoria").attr("value");
        val_lstZona = $("#lstZona").attr("value");
        if (val_lstCategoriaPrincipal == "") {
            condiciones = "Todas las zonas";
        } else {
            condiciones = lstCategoriaPrincipal;
        }
        if (val_lstCategoria == "") {
            condiciones += "";
        } else {
            condiciones += " - " + lstCategoria;
        }
        if (val_lstZona == "") {
            condiciones += "";
        } else {
            condiciones += " - " + lstZona;
        }
        $("#txtCondiciones").attr("value", condiciones);
        $('#frmPendientesxvendedor').attr('action', '/pdf/pendientesporvendedor');
        $('#frmPendientesxvendedor').submit();
    });

});

function listaZonaCobranza(idpadrec) {

    $.ajax({
        url: '/zona/listaCategoriaxPadre',
        type: 'post',
        datatype: 'html',
        data: {'idpadrec': idpadrec},
        success: function (resp) {
            console.log(resp);
            $('#lstCategoria').html(resp);

        },
        error: function (error) {
            console.log('error');
        },
        complete: function () {

        }

    });
}
function listaZonasxCategoria(idzona) {

    $.ajax({
        url: '/zona/listaZonasxCategoria',
        type: 'post',
        datatype: 'html',
        data: {'idzona': idzona},
        success: function (resp) {
            console.log(resp);
            $('#lstZona').html(resp);
        },
        error: function (error) {
            console.log('error');
        },
        complete: function () {

        }

    });
}