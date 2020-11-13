$(document).ready(function () {

    var title = "Datos Generales del Cliente";
    var title2 = "Medios de Contacto del Cliente";

    $('#txtBusqueda').autocomplete({
        source: "/cliente/autocomplete2/",
        select: function (event, ui) {
            elegircliente(ui.item.id);
        }}
    );

    $('#frmClienteVistaGeneral').submit(function () {
        if ($('#txtBusqueda').val().length == 0) {
            $('#txtBusqueda').attr('placeholder', 'Ingrese Razon Social o R.U.C. del Cliente');
            $('#txtBusqueda').focus();
            return false;
        }
    });

    $('#contenedorModal').css('overflow', 'auto').dialog({
        title: title,
        autoOpen: false,
        modal: true,
        width: 800,
        resizable: false,
        draggable: true,
    })
    
    $('#AgendaModal').css('overflow', 'auto').dialog({
        title: title2,
        autoOpen: false,
        modal: true,
        width: 600,
        resizable: false,
        draggable: true,
    })
    
    $('.verDetalle').live('click', function (e) {
        e.preventDefault();
        vercliente($(this).data('id'), 1);
        return false;
    });

    $('#imprimirModal').click(function (e) {
        e.preventDefault();
        imprSelec('modal');
    });

    $('.btnDxFacturacion').live('click', function (e) {
        cargaSucursal($(this).data('id'), 1);
        return false;
    });
    
    $('.btnDxDespacho').live('click', function (e) {
        cargaSucursal($(this).data('id'), 2);
        return false;
    });
    
    $('.btnCerrar').live('click', function (e) {
        $('#bloque_' + $(this).data('id')).html('');
        return false;
    });
    
    $('#abrirAgendaModel').click(function () {
        $('#AgendaModal').dialog('open');
        return false;
    });
    
    $('#txtCelular').keypress(function (e) {
        if (e.keyCode == 13) {
            guardarMedioContacto("Celular", $(this).val(), 7);  
        }
    });
    
    $('#grabarCelular').click(function () {
        guardarMedioContacto("Celular", $('#txtCelular').val(), 7);
        return false;
    });
    
    $('#editarCelular').click(function () {
        $('#lblCelular').html(' - Editar Celular: ');
        $('#txtCelular').val($('#DxAgendaCelular').html());
        return false;
    });
    
    $('#txtTelefono').keypress(function (e) {
        if (e.keyCode == 13) {
            guardarMedioContacto("Telefono", $(this).val(), 7);  
        }
    });
    
    $('#grabarTelefono').click(function () {
        guardarMedioContacto("Telefono", $('#txtTelefono').val(), 5);
        return false;
    });
    
    $('#editarTelefono').click(function () {
        $('#lblTelefono').html(' - Editar Telefono: ');
        $('#txtTelefono').val($('#DxAgendaTelefono').html());
        return false;
    });
    
    $('#txtEmail').keypress(function (e) {
        if (e.keyCode == 13) {
            guardarMedioContacto("Email", $(this).val(), 7);  
        }
    });
    
    $('#grabarEmail').click(function () {
        guardarMedioContacto("Email", $('#txtEmail').val(), 10);
        return false;
    });
    
    $('#editarEmail').click(function () {
        $('#lblEmail').html(' - Editar Email: ');
        $('#txtEmail').val($('#DxAgendaEmail').html());
        return false;
    });
    
    $('#modal').on('change', '#lstZona', function () {
        $('#imgchk').attr('style', 'display: none');
        $('#grabarZona').removeAttr('style');
    });
    
    $('#modal').on('click', '#grabarZona', function () {
        if ($('#lstZona').val() != '') {
            var tipo = 'Zona';
            $.ajax({
                url: '/cliente/listavistageneral_guardar',
                type: 'post',
                dataType: 'json',
                data: {'idcliente': $('#AgendaModal').data('id'), 'tipo': tipo, 'cadena': $('#lstZona').val()},
                success: function (resp) {                
                    if (resp.rspta) {
                        $('#imgchk').removeAttr('style');
                        $('#grabarZona').attr('style', 'display: none;');
                    } else {
                        alert('Error: No se pudo actualizar los cambios.');
                    }
                }, error: function (a, b, c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                }
            });
        } else {
            $('#lstZona').focus();
        }
        return false; 
    });
    
    $('#modal').on('click', '.grabarClienteZona', function () {
        var idclienteZona = $(this).data('id');
        var idzona = $('.lstClienteZona[data-id=' + $(this).data('id') + ']').val();        
        $.ajax({
            url: '/cliente/actualizarSucursal_2',
            type: 'post',
            dataType: 'json',
            data: {'idclienteZona': idclienteZona, 'idzona': idzona},
            success: function (resp) {                
                if (resp.rspta) {
                    $('#imgchk_' + idclienteZona).removeAttr('style');
                    $('.grabarClienteZona[data-id=' + idclienteZona + ']').attr('style', 'display: none;');
                } else {
                    alert('Error: No se pudo actualizar los cambios.');
                }
            }, error: function (a, b, c) {
                console.log(a);
                console.log(b);
                console.log(c);
            }
        });        
        return false;
    });
    
    $('#modal').on('change', '.lstClienteZona', function () {
        var idclienteZona = $(this).data('id');
        $('.grabarClienteZona[data-id=' + idclienteZona + ']').removeAttr('style');
    });
    
});

function elegircliente(idcliente) {
    var ruta = "/cliente/listavistageneralelegir/" + idcliente;
    $.post(ruta, function (data) {
        $('#txtBusqueda').val('');
        $('#tblClientes tbody').html(data);
    });
}

function cargaSucursal(idclientesucursal, tipo) {
    $.ajax({
        url: '/cliente/cargaSucursal',
        type: 'post',
        dataType: 'json',
        data: {'idclientesucursal': idclientesucursal},
        success: function (resp) {
            console.log(resp);            
            if (tipo == 1) {
                $('#bloque_' + idclientesucursal).html('<li><label><i>Datos de Facturacion: </i></label></li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Nombre Sucursal: </b> - ' + resp.nombresucursal + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Nombre Contacto: </b> - ' + resp.nomcontacto + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>DNI Contacto: </b> - ' + resp.dnicontacto + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Telefono Contacto: </b> - ' + resp.telcontac + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Celular Contacto: </b> - ' + resp.movilcontac + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Direccion Contacto: </b> - ' + resp.direccion_fiscal + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Zona: </b> - ' + resp.comboClienteZona + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Atencion: </b> - ' + resp.horarioatencion + '</li><br>');
            } else {
                $('#bloque_' + idclientesucursal).html('<li><label><i>Datos de Despacho: </i></label></li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Nombre Contacto de Despacho: </b> - ' + resp.nombrecontactodespacho + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>DNI Contacto de Despacho: </b> - ' + resp.dnidespacho + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Telefono Contacto de Despacho: </b> - ' + resp.telcontacdespacho + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Celular Contacto de Despacho: </b> - ' + resp.movilcontacdespacho + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Direccion de Despacho: </b> - ' + resp.direccion_despacho_contacto + '</li><br>');
                $('#bloque_' + idclientesucursal).append('<li><b>Atencion: </b> ' + resp.horarioatenciondespacho + '</li><br>');
            }
        }
    });
}

function guardarMedioContacto(tipo, cadena, longitud) {
    if ($('#lbl' + tipo).html() == ' - Editar ' + tipo + ': ') {
        longitud = -1;
    }
    if (cadena.length > longitud) {
        if ($('#lbl' + tipo).html() == ' - Nuevo ' + tipo + ': ') {
            if ($('#DxAgenda' + tipo).html() == '') {
                $('#DxAgenda' + tipo).html($('#txt' + tipo).val());
            } else {
                $('#DxAgenda' + tipo).append(' - ' + $('#txt' + tipo).val());
            }            
        } else {
            $('#DxAgenda' + tipo).html($('#txt' + tipo).val());
        }
        $.ajax({
            url: '/cliente/listavistageneral_guardar',
            type: 'post',
            dataType: 'json',
            data: {'idcliente': $('#AgendaModal').data('id'), 'tipo': tipo, 'cadena': $('#DxAgenda' + tipo).html()},
            success: function (resp) {                
                if (resp.rspta) {
                    $('#lbl' + tipo).html(' - Nuevo ' + tipo + ': ');
                    $('#txt' + tipo).val('');
                    vercliente($('#AgendaModal').data('id'), 0);
                } else {
                    alert('Error: No se pudo actualizar los cambios.');
                }
            }, error: function (a, b, c) {
                console.log(a);
                console.log(b);
                console.log(c);
            }
        });     
    } else {
        $('#txt' + tipo).focus();
    }
}

function vercliente(idcliente, abrir) {    
    var ruta = "/cliente/listavistageneralver/" + idcliente;
    $.ajax({
        url: ruta,
        type: 'post',
        dataType: 'json',
        success: function (resp) {
            $('#modal').html(resp.DatosGenerales);
            $('#DxAgendaCelular').html(resp.DatosCelular);
            $('#DxAgendaTelefono').html(resp.DatosTelefono);
            $('#DxAgendaEmail').html(resp.DatosEmail);
            $('#AgendaModal').data('id', resp.idCliente);
            
            $('#lblCelular').html(' - Nuevo Celular: ');
            $('#txtCelular').val('');
            $('#lblTelefono').html(' - Nuevo Telefono: ');
            $('#txtTelefono').val('');
            $('#lblEmail').html(' - Nuevo Email: ');
            $('#txtEmail').val('');
            
            if (abrir == 1) {
                $('#contenedorModal').dialog('open');
            }
        }, error: function (a, b, c) {
            console.log(a);
            console.log(b);
            console.log(c);
        }
    });
}