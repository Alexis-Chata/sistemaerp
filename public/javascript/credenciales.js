$(document).ready(function () {

    $('#txtActor1').autocomplete({
        source: "/actor/buscarAutocompleteUsuario/",
        select: function (event, ui) {
            $('#txtIdActor1').val(ui.item.id);
            listarResumenCredenciales(ui.item.id);
        }
    });

    $('#txtActor2').autocomplete({
        source: '/actor/buscarAutocompleteUsuario_ver2/',
        select: function (event, ui) {
            $('#txtIdActor2').val(ui.item.idactor);
        }
    })
    $('#txtModulo1').autocomplete({
        source: "/credenciales/buscarAutocompleteUrlModulo/",
        select: function (event, ui) {
            $('#txtIdModulo1').val(ui.item.id);
            //$('#codigoProducto').html(ui.item.value);
        }
    });
    $('#btnTraspasar').click(function (e) {
        e.preventDefault();
        txtIdActor1 = $('#txtIdActor1').attr("value");

        txtIdActor2 = $('#txtIdActor2').attr("value");
        if (txtIdActor1 != "" && txtIdActor2 != "") {
            $("#frmTraspasar").attr("action", '/credenciales/volcadocredenciales');
            $("#frmTraspasar").submit()
        } else {
            alert("coloque los usuarios");
        }
    });


    $('#btnBuscar').on('click', function () {
        txtModulo1 = $('#txtModulo1').attr("value");
        txtActor1 = $('#txtActor1').attr("value");
        idactor = $('#txtIdActor1').attr("value");
        idmodulo = $('#txtIdModulo1').attr("value");

        if (idmodulo == 0 || idmodulo == "") {
            alert("Ingrese Modulo");

        } else {
            $("#loadCredenciales").load("/credenciales/grillaAsignacionCredenciales/?&txtModulo1=" + txtModulo1 + "&txtIdActor1=" + idactor + "&txtIdModulo1=" + idmodulo);

        }

        $('#txtModulo1').attr("value", "");
        $('#txtActor1').attr("value", "");
        $('#txtIdModulo1').attr("value", 0);
        $('#txtIdActor1').attr("value", 0);
    });

    $('.asignarCredencial').live('click', function () {
        cx = $(this).attr("id");
        cx_valor = $(this).attr("value");
        if ($(this).attr('checked')) {
            cx_valor = 1;
        } else {
            cx_valor = 0;

        }
        grillamodulo = $(this).attr("x1");
        grillausuario = $(this).attr("x2");
        $.ajax({
            type: "GET",
            url: "/credenciales/asignarCredenciales/",
            data: "cx=" + cx + "&cx_valor=" + cx_valor + "&grillamodulo=" + grillamodulo + "&grillausuario=" + grillausuario,
            dataType: "json",
            success: function (data) {
                if (data.exito == true) {
                    alert("Guardo Correctamente");
                }
                if (data.exito == false) {
                    alert("No se pudo Guardar \n intente de nuevo");
                }
            }
        });
    });

    $('.credencialesdesc').live('click', function () {
        if ($('#chkONOFF').attr('checked')) {
        } else {
            id = $(this).attr("name");
            descripcion = $("#" + id).attr("value");
            idmodulo = $("#" + id).attr("val");

            $.ajax({
                type: "GET",
                url: "/credenciales/asignarCredencialesDesc/",
                data: "descx=" + id + "&descx_valor=" + descripcion + "&descx_idmodulo=" + idmodulo,
                dataType: "json",
                success: function (data) {
                    if (data.exito == true) {
                        alert("Guardo Correctamente");
                    }
                    if (data.exito == false) {
                        alert("No se pudo Guardar \n intente de nuevo");
                    }
                }
            });
        }
    });

    $('#chkONOFF').live('click', function () {
        if ($(this).attr('checked')) {
            cx_valor = 1;
            $(".cajas").attr("disabled", true);
            $("#labelONOFF").html("OFF");
        } else {
            cx_valor = 0;
            $(".cajas").attr("disabled", false);
            $("#labelONOFF").html("ON");

        }
    });


});

function listarResumenCredenciales(txtIdActor1) {
    $("#loadTabla").load("/credenciales/listarResumenCredenciales/?txtidActor1=" + txtIdActor1);

}

