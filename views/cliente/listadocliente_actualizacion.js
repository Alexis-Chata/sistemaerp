$(document).ready(function () {
    
    $('#chkF1').change(function () {
        armar_filtro();
    });
    
    $('#chkF2').change(function () {
        armar_filtro();
    });
    
    $('#chkF3').change(function () {
        armar_filtro();
    });
    
    $('#txtCliente').autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.ajax({
                url: "/cliente/autocomplete3/",
                dataType: "json",
                data: {term: request.term, filtro: $('#idFiltro').data('dx')},
                success: function (data) {
                    console.log(data);
                    response(data);
                }
            });
        },
        select: function(event, ui){
            if ($('#Cli' + ui.item.id).data('est') == undefined) {
                var tempActualizado = "<label style='color: red;'>NO ACTUALIZADO</label>";
                if (ui.item.actualizado == 1) {
                    var tempActualizado = "<label style='color: blue;'>ACTUALIZADO</label>";
                } else if (ui.item.actualizado == 2) {
                    var tempActualizado = "<label style='color: green;'>PARA EL FINAL</label>";
                }
                var clienteSelec = '<tr id="Cli' + ui.item.id + '" data-est="1">';
                clienteSelec += '<td>' + ui.item.id + '</td>';
                clienteSelec += '<td>' + ui.item.label + '</td>';
                clienteSelec += '<td>' + ui.item.rucdni + '</td>';
                clienteSelec += '<td>' + ui.item.distritociudad + '</td>';
                clienteSelec += '<td>' + ui.item.direccion + '</td>';
                clienteSelec += '<td>' + ui.item.telefono + '</td>';
                clienteSelec += '<td>' + ui.item.celular + '</td>';
                clienteSelec += '<td>' + tempActualizado + '</td>';
                clienteSelec += '<td><a href="/cliente/actualizar/' + ui.item.id + '"><img src="/imagenes/editnew.png"></a></td>';
                $('#tblresultado tbody').append(clienteSelec);   
                $('#tblresultado').removeAttr('style');
            }            
        }
    });
    
    $('#txtCliente').keypress(function (e) {
        if (e.keyCode == 13) {
            $(this).val('');
        }
    });

    $("#filtro").change(function () {
        var id = $(this).val();
        var url = '/cliente/desactualizados/' + id;
        window.location = url;
    });

    $("#seleccion").change(function () {
        var id = $("#seleccion option:selected").text();
        var url = '/cliente/desactualizados/' + $(this).data('filtro') + '.' + id;
        window.location = url;
    });

});

function armar_filtro() {
    var CHKfiltro = '-1';
    if( $('#chkF1').prop('checked') ) {
        CHKfiltro = CHKfiltro + ', 0';
    }
    if( $('#chkF2').prop('checked') ) {
        CHKfiltro = CHKfiltro + ', 1';
    }
    if( $('#chkF3').prop('checked') ) {
        CHKfiltro = CHKfiltro + ', 2';
    }
    $('#idFiltro').data('dx', CHKfiltro);
}