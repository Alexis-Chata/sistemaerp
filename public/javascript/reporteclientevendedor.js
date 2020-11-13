$(document).ready(function () {
    $('#txtVendedor').autocomplete({
        source: "/vendedor/autocompletevendedor/",
        select: function (event, ui) {
            $('#idVendedor').val(ui.item.id);
        }
    });
    
    $("#btnRanking").click(function () {
        $("#Parametros").attr("action", "/pdf/reporteclientevendedor/");
    });
    
    $("#btnResumido").click(function () {
        $("#Parametros").attr("action", "/excel/reporteclientevendedorresumido/");
    });

});