$(document).on('ready', function () {
    var msgboxTitleProveedor = 'Mantenimiento de Proveedor';
    $('#frmProveedorNuevo, #frmProveedorActualizar').validate({
        invalidHandler: function (form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                $.msgbox(msgboxTitle, 'Ingrese todos los datos requeridos correctamente');
            }
        },
        errorElement: 'span'
    });
    /*cancelar registro*/
    $('#btnCancelar').on('click', function (e) {
        e.preventDefault();
        window.location = '/proveedor/listado';
    });
    /*Boton de eliminacion*/
    $('.btnEliminar').on('click', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.msgbox(msgboxTitleProveedor, '¿Esta seguro de elimiar el registro?');
        $('#msgbox-ok').click(function () {
            window.location = url;
        });
    });

    $('#contenedor').dialog({
        width: 620,
        height: 350,
        rezisable: false,
        autoOpen: false,
        modal: true,
        close: function () {
            //$('#frmNuevo')[0].reset();
        }
    });

    $('#btnNuevo').click(function () {
        limpiar();
        $('#contenedor').dialog('open');
        return false;
    });

    $('#tblPersona').on('click', '.btnEditarPersona', function () {     
        limpiar();
        verpersona($(this).data('id'));
        return false;
    });
    
    $('#tblPersona').on('click', '.btnEliminarPersona', function () {     
        if (confirm('¿Esta seguro de eliminar a la persona?')) {
            window.location.href = '/proveedor/eliminapersona/' + $(this).data('id');
        } else {
            return false;
        }        
    });    
    
    $('#btnGrabar').click(function () {
        $.ajax({
            url: '/proveedor/personagrabaedita',
            type: 'post',
            dataType: 'json',
            data: {
                'contacto' :$('#idcontacto').val(),
                'idproveedor' :$('#idProveedor').val(),
                'idproveedorpersona' :$('#idproveedorpersona').val(),
                'cargo' :$('#idcargo').val(),
                'email' :$('#idemail').val(),
                'telefono' :$('#idtelefono').val() 
            }, success: function (resp) {                
                $('#tblPersona tbody').html(resp.resultados);
                $('#contenedor').dialog('close');
            }
        });
    });

});

function verpersona(idproveedorpersona) {
    $.ajax({
        url: '/proveedor/persona',
        type: 'post',
        dataType: 'json',
        data: {'idproveedorpersona': idproveedorpersona},
        success: function (resp) {
            if (resp.estado == 1) {
                $('#idcontacto').val(resp.contacto);
                $('#idproveedorpersona').val(resp.idproveedorpersona);
                $('#idcargo').val(resp.cargo);
                $('#idemail').val(resp.email);
                $('#idtelefono').val(resp.telefono);            
                $('#contenedor').dialog('open');
            }            
        }
    });
}

function limpiar() {
    $('#idcontacto').val('');
    $('#idproveedorpersona').val('');
    $('#idcargo').val('');
    $('#idemail').val('');
    $('#idtelefono').val('');
}