var idcelular=1, idtelefono=1;

$(document).on('ready', function () {
    
    $("body").on('focusout', 'input[type=hidden], input[type=text], textarea, input[type=email]', function() {
        var str = $(this).val();
        str = str.trim();
        $(this).val(str.replace(/\s+/g, ' '));
        limpiarEspeciales(this);
    });
    
    $('#cmbdir1').change(function () {
        if ($(this).val() == 'MERCADO' || $(this).val() == 'C.C.' ) {
            $('.block-nro').attr('style', 'display: none');
        } else {
            $('.block-nro').removeAttr('style');
        }
    });
    
    $('#cmbdir2').change(function () {
        if ($(this).val() != 0) {
            $('#textDir2').removeAttr('disabled');
            $('#textDir2').focus();
        } else {
            $('#textDir2').attr('disabled', 'disabled');
        }
    });
    
    $('#cmbdir3').change(function () {
        if ($(this).val() != 0) {
            $('#textDir3').removeAttr('disabled');
            $('#textDir3').focus();
        } else {
            $('#textDir3').attr('disabled', 'disabled');
        }
    });
    
    $('#btnDirCopy').click(function () {
        $('#textDireccion').val($('#txtDirCopy').text());
        $('#textDireccion').focus();
        return false;
    });
    
    $('#btnTelfCopy').click(function () {
        $('#textTelf').val($('#txtTelfCopy').text());
        $('#textTelf').focus();
        return false;
    });
    
    $('#textTelf').keypress(function (e) {
        if (e.keyCode == 13) {
            if ($(this).val().length > 5) {
                if ($('.EliTelf[data-telf="' + $(this).val() + '"]').data('id') == undefined) {
                    $('#AgTelf').append('<tr id="T' + idtelefono + '"><td>' + $(this).val() + '</td><td><a href="#" title="Eliminar" class="EliTelf" data-telf="' + $(this).val() + '" data-id="' + idtelefono + '"><b>X</b></a></td></tr>');
                    $('#limpTelf').html('');
                    idtelefono++;
                }   
                $(this).val('');
            }          
        }
    });
    
    $('#AgTelf').on('click', '.EliTelf', function () {
        $('#T' + $(this).data('id')).remove();
        if ($('#AgTelf').html().length == 0) {
            $('#limpTelf').html('<tr><th colspan="2">Vacio</th></tr>');
        }
        return false;
    });    
    
    $('#btnCelCopy').click(function () {
        $('#textCel').val($('#txtCelCopy').text());
        $('#textCel').focus();
        return false;
    });
    
    $('#textCel').keypress(function (e) {
        if (e.keyCode == 13) {
            if ($(this).val().length > 7) {
                if ($('.EliCel[data-cel="' + $(this).val() + '"]').data('id') == undefined) {
                    $('#AgCel').append('<tr id="C' + idcelular + '"><td>' + $(this).val() + '</td><td><a href="#" title="Eliminar" class="EliCel" data-cel="' + $(this).val() + '" data-id="' + idcelular + '"><b>X</b></a></td></tr>');
                    $('#limpCel').html('');
                    idcelular++;
                }  
                $(this).val('');
            }          
        }
    });
    
    $('#AgCel').on('click', '.EliCel', function () {
        $('#C' + $(this).data('id')).remove();
        if ($('#AgCel').html().length == 0) {
            $('#limpCel').html('<tr><th colspan="2">Vacio</th></tr>');
        }
        return false;
    });   
    
    $('#chkConf').click(function () {
        if($('#chkConf').prop('checked') ) {
            if ($('#cmbdir1').val() != 0 && $('#textDireccion').val().length > 0) {
                var acumulado = ''; 
                if ($('#cmbdir2').val() == 0 || $('#textDir2').val().length == 0) {
                    $('#cmbdir2').val(0);
                    $('#textDir2').val('');
                } else {
                    acumulado = ' ' + $('#cmbdir2').val() + ' ' + $('#textDir2').val(); 
                }
                if ($('#cmbdir3').val() == 0 || $('#textDir3').val().length == 0) {
                    $('#cmbdir3').val(0);
                    $('#textDir3').val('');
                } else {
                    acumulado = acumulado + ' ' + $('#cmbdir3').val() + ' ' + $('#textDir3').val(); 
                }
                var camponro = '';
                if ($('#cmbdir1').val() == 'MERCADO' || $('#cmbdir1').val() == 'C.C.' ) {
                    $('#textNro').val('');
                } else {
                    if ($('#textNro').val().length == 0) {
                        camponro = 'S/N';
                        $('#textNro').val('S/N');
                    } else {
                        camponro = ' NRO. ' + $('#textNro').val();
                    }
                }                
                $('#form-direccion').val($('#cmbdir1').val() + ' ' + $('#textDireccion').val() + camponro + acumulado);
                $('#form-referencia').val($('#textReferencia').val());
                $('#form-distrito').val($('#lstDistrito').val());
                acumulado = ''; 
                var band = 0;
                $('.EliCel').each(function () {
                    if (band == 0) {
                        acumulado = $(this).data('cel');
                        band = 1;
                    } else {
                        acumulado += ' / ' + $(this).data('cel');
                    }                
                });
                $('#form-celular').val(acumulado);
                acumulado = '';
                band = 0;
                $('.EliTelf').each(function () {
                    if (band == 0) {
                        acumulado = $(this).data('telf');
                        band = 1;
                    } else {
                        acumulado += ' / ' + $(this).data('telf');
                    }                
                });
                $('#form-telefono').val(acumulado);
            } else {
                limpiar_formulario();
                $.msgbox('Actualizacion de Cliente', 'Dirección no detectada.');
		execute();
                return false;
            }            
        } else {
            limpiar_formulario();
        }
    });
    
    $('#registrarCli').click(function () {
        if(!$('#chkConf').prop('checked') ) {
            $('#chkConf').focus();
        } else {
            $('#form-actualizacion').submit();
        }        
    });
    
    $('#lstDepartamento, #lstProvincia, #lstDistrito, #cmbdir1, #textDireccion, #textNro, #cmbdir2, #textDir2, #cmbdir3, #textDir3, #textReferencia, #textTelf, #textCel').change(function () {
        $("#chkConf").prop("checked", "");
    });
    
});

function limpiar_formulario () {
    $('#form-distrito').val('');
    $('#form-direccion').val('');
    $('#form-referencia').val('');
    $('#form-celular').val('');
    $('#form-telefono').val('');
}

function limpiarEspeciales(campo) {
    var str = $(campo).val();
    var ltr = ['[ÀÁÂÃÄ]', '[ÈÉÊË]', '[ÌÍÎÏ]', '[ÒÓÔÕÖ]', '[ÙÚÛÜ]', 'Ç', '[ÝŸ]'];
    var rpl = ['A', 'E', 'I', 'O', 'U', 'C', 'Y'];
    str = String(str.toUpperCase());

    for (var i = 0, c = ltr.length; i < c; i++) {
        var rgx = new RegExp(ltr[i], 'g');
        str = str.replace(rgx, rpl[i]);
    }
    $(campo).val(str);
}