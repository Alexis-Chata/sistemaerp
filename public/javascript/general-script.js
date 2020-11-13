$(document).ready(function () {
    //Definir al acceso a las URLs
    //esto es para que funcione en datepicker en opera
    if (navigator.userAgent.indexOf('Opera') >= 0) {
        $('.datepicker').datepicker({
            dateFormat: 'yy/mm/dd',
            dayNamesMin: ['Dom', 'Lu', 'Ma', 'Mi', 'Je', 'Vi', 'Sa'],
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
        });
    }
    $('#tabs').tabs();
    //imprimir zona o area
    //bloquear el menu derecho
    //document.oncontextmenu = function(){return false}
    //blorquear la seleccion
    /*function disableselect(e){
     return false
     }
     function reEnable(){
     return true
     }
     document.onselectstart=new Function ("return false")
     if (window.sidebar){
     document.onmousedown=disableselect
     document.onclick=reEnable
     }
     $(document).mouseleave(function(e){
     $('#txtBusqueda').focus();
     
     });*/


    /*Definicion del ancho de los lables que estan dentro de la clase .inline-block*/
    $('.inline-block').each(function () {
        var maxWidth = $(this).find('label').maxWidth() + "px";
        $(this).find('label').not('.inline').css('width', maxWidth);
    });

    $('.title').each(function () {
        var index = $('.title').index(this);
        if (index < 1) {
            $(this).append('<a href="#" class="show" title="Ocultar">-</a>');
        } else {
            $(this).append('<a href="#" class="show" title="Mostrar">+</a>');
        }
    });
    $('.show').on('click', function () {
        if ($(this).text() == '+') {
            $(this).attr('title', 'Ocultar').html('-');
            $(this).parent().next('div').slideDown('fast');
            $(this).parents('.field-set').find('.addicional-informacion').html('');
        } else {
            var information = $(this).parent().next('div').find('.important').val();
            $(this).attr('title', 'Mostrar').html('+');
            $(this).parent().next('div').slideUp('fast');
            $(this).parents('.field-set').find('.addicional-informacion').html('(' + information + ')');
        }
    });

    $('.body:gt(0)').hide();
    /*datePicker*/
    $('.datePicker').datepicker({
        dateFormat: 'yy/mm/dd',
        showOn: 'both',
        buttonText: 'Selecciona una fecha',
        buttonImage: '/imagenes/calendar.png',
        buttonImageOnly: true,
        dayNames: ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
        dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre"],
        nextText: "Siguiente",
        prevText: "Anterior"
    });
    $('form ul li input[type="submit"]').parent().css('text-align', 'center');
    /*$('.btnEliminar').click(function(e){
     e.preventDefault();
     $row=$(this).attr('href');
     $.msgbox("Eliminaci&oacute;n de Registro","&iquest;Est&aacute; seguro de eliminar el registro?",'<a href="' + $row + '">Si</a>');
     });*/
    /*$('.btnEditar').click(function(e){
     e.preventDefault();
     $row=$(this).attr('href');
     $.msgbox("Actualizaci&oacute;n de Registro", "&iquest;Est&aacute; seguro de actualizar el registro?",'<a href="' + $row + '">Si</a>');
     });*/

    $('body').on('keyup', '.uppercase', function () {
        $(this).val($(this).val().toUpperCase());
    });

    //Arbol de menu
    $('#menuizq').treeview({
        collapsed: true,
        animated: "medium",
        //persist: "location"
    });

    /* dar estilo de BUTTON */
    $('input[type=submit],button').addClass('button');

    /* nota de credito */
    $('#txtNumeroFactura').autocomplete({
        source: "/facturacion/autocompletefactura/",
        select: function (event, ui) {
            $('#idDocumento').val(ui.item.id);
            $('#txtIdOrden').val(ui.item.idorden);
            buscaOrdenVenta();
            cargaDetalleOrdenVenta();
        }
    });


    /*Validaciones*/
    $('body').on('keydown', 'input.numeric', function (e) {
        if (e.keyCode == 110 || e.keyCode == 190 || e.keyCode == 8 || e.keyCode == 9 || (e.keyCode >= 37 && e.keyCode <= 40)) {

        } else if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    //Variables generales
    //msgboxTitle = "Orden de Compra Nuevo";

    /*Mantenimiento de producto
     -------------------------------------------------------------- */

    //Producto nuevo
    var idLineaNuevo = $('#lstLineaNuevo option:selected').val();
    $('#lstLineaNuevo').change(function () {
        cargaSublinea1();
    });
    $('#fotoProducto').hide();
    $('#chkCambiarFoto').change(function () {
        fotoProducto = $('#fotoProducto');
        if ($(this).is(':checked')) {
            $(fotoProducto).show().attr({'disabled': false, 'class': "required"});
        } else {
            $(fotoProducto).hide().attr({'disabled': true, 'class': ""}).next('.error').remove();
        }
    });
    //Verificar si codigo de producto es correcto
    $('#txtCodigoProductoNuevo').blur(verificaCodigoProducto);
    //Registro del producto nuevo
    $('#frmProductoNuevo').submit(function () {
        if (!$('.required').valida()) {
            return false;
        } else if (!verificaFormatoImagen()) {
            return false;
        } else {
            return true;
        }
    });

    //Vendedores
    $('#lstVendedores').change(function () {
        cargaVendedores();
    });

    //carga zonas
    $('#lstZonas').change(function () {
        cargaCuentasxZonas();
    });

    //carga departamento
    $('#lstPais').change(function () {
        cargaDepartamentos();
    });

    //carga provincia	
    $('#lstDepartamento').change(function () {
        cargaProvincias();
    });

    //carga distrito
    $('#lstProvincia').change(function () {
        cargaDistritos();
    });

    //carga area
    $('#lstDistrito').change(function () {
        cargaAreas();
    });

    //cargar guias pedido por cliente
    $('#lstClientes').change(function () {
        cargaGuiasPedidoxCliente();
    });

    //cargar cuentas por guias de pedido
    $('#lstGuiasPedido').change(function () {
        cargaOrdenPagoxGuia();
        cargaLetraxGuia();
    });
    //seleccionar categoria de zona
    $('#lstCategorias').change(function () {
        idcategoria = $('#lstCategorias option:selected').val();
        if (idcategoria != "") {
            var url = "/zona/buscar/" + idcategoria;
            $('#aeditarcategoria').attr('href', "/zona/editarcategoria/" + idcategoria);
            $('#idcategoria').attr('value', idcategoria);
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                success: function (result) {
                    $("#datagridedit").data("kendoGrid").dataSource.data(result);
                }
            });
        } else {
            $('#aeditarcategoria').attr('href', '#');
            $('#idcategoria').attr('value', '');
        }
    });

    //seleccionar linea padre
    $('#lstLineasPadre').change(function () {
        idlineapadre = $('#lstLineasPadre option:selected').val();
        if (idlineapadre != "") {
            $('#aeditarlineapadre').attr('href', "/linea/editarlineapadre/" + idlineapadre);
            $('#idlineapadre').attr('value', idlineapadre);
            var url = "/linea/buscar/" + idlineapadre;
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                success: function (result) {
                    $("#datagridedit").data("kendoGrid").dataSource.data(result);
                }
            });
        } else {
            $('#aeditarlineapadre').attr('href', '#');
            $('#idlineapadre').attr('value', '');
        }
    });

    /*ocultar mostrar datos naturales, juridicos*/

    $('.pnatural').hide();
    $('.pjuridica').hide();
    $('#lstTipoCliente').change(function () {
        tipo = $(this).val();
        if (tipo == 1) {
            $('.pjuridica').hide();
            $('.pnatural').show();

            $('#apellido1').attr('required', 'required');
            $('#apellido2').attr('required', 'required');
            $('#nombrecli').attr('required', 'required');
            $('#dni').attr('required', 'required');
            $('#razonsocial').removeAttr('required');

        } else if (tipo == 2) {
            $('.pnatural').hide();
            $('.pjuridica').show();

            $('#razonsocial').attr('required', 'required');
            $('#apellido1').removeAttr('required');
            $('#apellido2').removeAttr('required');
            $('#nombrecli').removeAttr('required');
            $('#dni').removeAttr('required');
        } else {
            $('.pnatural').hide();
            $('.pjuridica').hide();
        }
    });

    $('#lstTipoCliente').change();

    /*cargar la tabla de guias*/
    $('#btnMostrarCuentas').click(function (e) {
        e.preventDefault();
        $('#frmBusqueda').submit();
        //cargaOrdenesVentaxCliente();
    });

    //Actualizaci�n de producto
    $('#lstLineaEditar').change(function () {
        cargaSublinea2($('#lstLineaEditar option:selected').val());
    });
    //validar antes de enviar
    $('#frmProductoActualiza').submit(function () {
        if (!$('input.required').valida()) {
            return false;
        } else {
            return true;
        }
    });

    /* efectos en tabla */

    $('body').on('mouseover', '.tablaresalto tbody tr', function (event) {
        $(this).addClass("filaseleccionada");
    });
    $('body').on('mouseout', '.tablaresalto tbody tr', function (event) {
        $(this).removeClass("filaseleccionada");
    });
    $('body').on('click', '.tablaresalto tbody tr', function (event) {
        cargarCuentasxOrdenVenta(this.id);
    });

    /*General Script
     -------------------------------------------------------------- */
    //Paginacion
    var paginaActual = $('#txtPaginaActual').val();
    var numeroPaginas = $('#txtNumeroPaginas').val();
    var rutaBtnSiguiente = $('#btnSiguiente').attr('href') + (parseInt(paginaActual) + 1);
    var rutaBtnAnterior = $('#btnSiguiente').attr('href') + (parseInt(paginaActual) - 1);
    $('#btnSiguiente').attr('href', rutaBtnSiguiente);
    $('#btnAnterior').attr('href', rutaBtnAnterior);
    $('#btnAnterior').click(function (e) {
        e.preventDefault();
        if (paginaActual == 1) {
            return false;
        } else {
            $('#txtPagina').val(parseInt(paginaActual) - 1);
            $('#frmBusqueda').submit();
        }
    });
    $('#btnSiguiente').click(function (e) {
        e.preventDefault();
        if (parseInt(paginaActual) < parseInt(numeroPaginas)) {
            $('#txtPagina').val(parseInt(paginaActual) + 1);
            $('#frmBusqueda').submit();
        }
    });
    $('#btnUltimo').click(function (e) {
        e.preventDefault();
        $('#txtPagina').val(numeroPaginas);
        $('#frmBusqueda').submit();
    });
    $('#btnPrimero').click(function (e) {
        e.preventDefault();
        $('#txtPagina').val(1);
        $('#frmBusqueda').submit();
    });
    /*Autocompletes*/
    //Autocomplete Producto
    $('#txtCodigoProducto').autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.ajax({
                url: "/producto/buscarautocomplete/",
                dataType: "json",
                data: {term: request.term, idlinea: $('#lstLinea option:selected').val()},
                success: function (data) {
                    console.log(data);
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $('#txtIdProducto').val(ui.item.id);
            $('#txtTituloProducto').val(ui.item.tituloProducto);
            $('#txtCantidadProducto').focus();
            
        }
    });

    $('#txtCodigoProductoj').autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.ajax({
                url: "/producto/buscarautocompletej/",
                dataType: "json",
                data: {term: request.term, idlinea: $('#lstLinea option:selected').val()},
                success: function (data) {
                    console.log(data);
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $('#txtIdProducto').val(ui.item.id);
            $('#txtTituloProducto').val(ui.item.tituloProducto);
            $('#txtCantidadProducto').focus();
        }
    });



    //Autocomplete Producto para Importaciones
    $('#txtCodigoProductoCompras').autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.ajax({
                url: "/producto/buscarAutocompleteCompras/",
                dataType: "json",
                data: {term: request.term, idlinea: $('#lstLinea option:selected').val()},
                success: function (data) {
                    console.log(data);
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $('#txtIdProducto').val(ui.item.id);
            $('#txtTituloProducto').val(ui.item.tituloProducto);
        }
    });
    /*Autocomplete transporte*/
    $('body').on('keydown.autocomplete', '#txtTransporte', function () {
        $(this).autocomplete({
            source: "/ventas/autocompleteTransporte/",
            select: function (event, ui) {
                $('#txtIdTransporte').val(ui.item.id);
                $('#txtRucTransporte').val(ui.item.ruc);
                $('#txtTelefonoTransporte').val(ui.item.telefono);
                $('#txtDireccionTransporte').val(ui.item.direccion);
                $('#txtDireccionTransporte').focus();
            }
        });
    });
    $('#txtBusqueda').autocomplete({
        //source: personas
        source: function (request, response) {
            $.ajax({
                url: $('#urlautocomplete').val() + $('#txtBusqueda').val(),
                dataType: "json",
                data: {term: request.term, idcategoria: $('#lstCategorias option:selected').val()},
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $('#txtIdProveedor').val(ui.item.id);
            $('#idcliente').val(ui.item.id);
        }
    });
    /*seleccionar cliente*/
    $('#btnSeleccionarCliente').click(function (e) {
        e.preventDefault();
        page = "/cliente/datosclientexid";
        $.post(page, {idcliente: $('#idcliente').val()}, function (data) {
            $('#btnMostrarCuentas').removeAttr("disabled");
            $('#datosCliente').html(data);
        });
        var url = $('#urlpagination').val() + $('#idcliente').val();
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: {term: $('#idcliente').val()},
            success: function (result) {
                //console.log(result);
                if (result != null) {
                    $("#datagrid").data("kendoGrid").dataSource.data(result);

                } else {
                    $("#datagrid").data("kendoGrid").dataSource.data("");
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });
    /*busqueda
     $('tfoot a').click(function(e){
     e.preventDefault();
     $('#txtPagina').val($(this).html());
     $('#frmBusqueda').submit();
     });*/

    /* columnas */

    //columnas=[{ field: "idalmacen", title: "Id"},{ field: "nomalm", title: "Nombre"},{ field: "rucalm", title:"RUC"},{ field: "diralm", title:"Direccion"},{ command: { text: "Editar", click: actualizaRegistro }, title: "editar ", width: "110px" }];
    var datath = $("#datagridedit").find("th");
    var total = datath.length;
    var columnas = [];
    for (var i = 0; i < total; i++) {
        columnas.push({
            field: datath.eq(i).attr("data-field"),
            title: datath.eq(i).html()
        });
    }
    columnas.push({command: {text: "Editar", click: actualizaRegistro}, title: "editar", width: "110px"});

    /*PAGINACION CON KENDO*/
    $("#datagridedit").kendoGrid({
        dataSource: {
            transport: {
                read: $('#urlpagination').val()
            },
            /*schema: {
             //data: 'data',
             model: {
             fields: {
             nompro: {type: 'string'},
             stockactual: {type: 'number'},
             stockdisponible: {type: 'number'}
             }
             }
             },*/
            pageSize: 10,
            batch: true
        },
        filterable: true,
        //selectable: "cell",
        resizable: true,
        height: 450,
        pageable: {
            refresh: true,
            pageSizes: true
        },
        sortable: true,
        //editable: true,
        /*editable: { //disables the deletion functionality 
         update: true, 
         destroy: false
         },*/
        //change: onChange,
        //toolbar: [ "save", "cancel" ],  // adds save and cancel buttons
        columns: columnas

    });

    $("#datagrid").kendoGrid({
        dataSource: {
            transport: {
                read: $('#urlpagination').val()
            },
            pageSize: 10
        },
        filterable: true,
        resizable: true,
        height: 450,
        pageable: {
            refresh: true,
            pageSizes: true
        },
        sortable: true
    });
    $("#dataGridReport").kendoGrid({
        dataSource: {
            type: "odata",
            pageSize: 10
        },
        filterable: true,
        height: 450,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true
        }
    });
    columnas2 = columnas.push({command: {text: "Detalles", click: mostrarDetalles}, title: "detalles", width: "110px"});
    $("#dataGridDetalle").kendoGrid({
        dataSource: {
            pageSize: 10
        },
        filterable: true,
        height: 450,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true
        }
    });

    /*Extendiendo Jquery validate*/
    $.extend($.validator, {
        messages: {
            required: '*',
            remote: 'Porfavor, corrije el valor de este campo.',
            email: 'Ingresa una dirección de correo válida.',
            url: 'Ingresa una URL válida.',
            date: 'Ingresa una fecha válida.',
            dateISO: 'Ingresa una fehca válida (ISO).',
            number: 'Ingresa un número válido.',
            digits: 'Ingresa sólo letras.',
            creditcard: 'Ingresa un número de tarjeta de crédito válido.',
            equalTo: 'Ingresa el nuevo valor de nuevo.',
            accept: 'Ingresa un valor con extensión válida.',
            maxlength: $.validator.format('Porfavor ingresa menos de {0} caractéres.'),
            minlength: $.validator.format('Porfavor ingresa al menos {0} caractéres.'),
            rangelength: $.validator.format('Porfavor ingresa un valor entre {0} y {1} caractéres de longitud.'),
            range: $.validator.format('Ingresa un valor entre {0} y {1}.'),
            max: $.validator.format('Ingresa un valor menor o igual que {0}.'),
            min: $.validator.format('*')
        }
    });
    $.validator.setDefaults({
        errorElement: 'span',
        invalidHandler: function (form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                $.msgbox(msgboxTitle, 'Ingrese todos los datos requeridos correctamente');
            }
        },
    })

});

/*cambios en grid*/
function onChange(arg) {
    var arr = Array();
    total = this.select().find('td').length;
    for (var i = 0; i < total; i++) {
        arr[i] = this.select().find('td').eq(i).html();
    }
}
/*mostrar detalles*/
function mostrarDetalles(e) {
    e.preventDefault();


}

/*grbar nuevo registro*/
function actualizaRegistro(e) {
    e.preventDefault();
    var datatr = $(e.currentTarget).closest("tr");
    id = datatr.find("td").html();
    var page = $('#urleditar').val() + id;
    window.location = page;
}

/*cargar zonas*/
function cargarzonasxnombre() {
    var text = $('#txtbuscarzona').val();
    var page = "/zona/buscarzonaxnombre/" + text;
    if (text != '') {
        $.post(page, function (data) {
            $('#tblzonas tbody').html(data);
        });
    }
}
function cargarzonasxid() {
    var id = $('#txtidzona').val();
    var page = "/zona/buscarzonaxid/" + id;
    if (id != '') {
        $.post(page, function (data) {
            $('#tblzonas tbody').html(data);
        });
    }
}


/*cargar vendedores*/
function cargaVendedores() {
    var idVendedor = $('#lstVendedores option:selected').val();
    var page = "/cobranza/cargarListadoxVendedor/" + idVendedor;
    if (idVendedor != '') {
        $.post(page, function (data) {
            $('#tblCuentasxVendedor tbody').html(data);
        });
    }
}

/*cargar departamentos*/
function cargaDepartamentos() {
    var codPais = $('#lstPais option:selected').val();
    var page = "/departamento/listaroptions/" + codPais;
    if (codPais != '') {
        $.post(page, function (data) {
            $('#lstDepartamento').html('<option value="">Departamento</option>' + data);
        });
    }
}

/*cargar provincias*/
function cargaProvincias() {
    var idDepartamento = $('#lstDepartamento option:selected').val();
    var page = "/provincia/listaroptions/" + idDepartamento;
    if (idDepartamento != '') {
        $.post(page, function (data) {
            $('#lstProvincia').html('<option value="">Provincia</option>' + data);
            $('#lstDistrito').html('<option value="">Distrito</option>');
        });
    }
}

/*cargar distritos*/
function cargaDistritos() {
    var rel = $('#lstProvincia option:selected').val();
    var page = "/distrito/listaroptions/" + rel;
    if (rel != '') {
        $.post(page, function (data) {
            $('#lstDistrito').html('<option value="">Distrito</option>' + data);
        });
    }
}

/*cargar areas*/
function cargaAreas() {
    var idDistrito = $('#lstDistrito option:selected').val();
    var page = "/area/listaroptions/" + idDistrito;
    if (idDistrito != '') {
        $.post(page, function (data) {
            $('#lstArea').html('<option value="0">ninguno' + data);
        });
    }
}

/*cargar cuentas por zonas*/
function cargaCuentasxZonas() {
    var idZona = $('#lstZonas option:selected').val();
    var page = "/cobranza/cargarListadoxZona/" + idZona;
    if (idZona != '') {
        $.post(page, function (data) {
            $('#tblCuentasxZona tbody').html(data);
        });
    }
}

/*cargar ordenes pago*/
function cargaOrdenPagoxGuia() {
    var idGuia = $('#lstGuiasPedido option:selected').val();
    var page = "/cobranza/cargarOrdenesPago/" + idGuia;
    var page2 = "/cobranza/cargardatosguia/" + idGuia;
    if (idGuia != '') {
        $.post(page2, function (data) {
            $('#datosguia').html(data);
        });
        $.post(page, function (data) {
            $('#tblOrdenesPago tbody').html(data);
        });
    }
}
/*cargar letras*/
function cargaLetraxGuia() {
    var idGuia = $('#lstGuiasPedido option:selected').val();
    var page = "/cobranza/cargarLetrasxGuia/" + idGuia;
    if (idGuia != '') {
        $.post(page, function (data) {
            $('#tblLetrasxGuia tbody').html(data);
        });
    }
}

function cargarCuentasxOrdenVenta(idordenventa) {
    var page = "/cobranza/cargarCuentasxOrdenVenta/" + idordenventa;
    if (idordenventa != '') {
        $.post(page, function (data) {
            $('#titulotablaletras').html('Letras de la Orden de Pago No ' + idordenventa);
            $('#tblCuentasxOrden tbody').html(data);
        });
    }
}


/*cargar guias de pedido por cliente*/
function cargaOrdenesVentaxCliente() {
    var pagina = $('#txtPaginaActual').val();
    var paginacion = $('#txtNumeroPaginas').val();
    var page = "/cobranza/estcuentas/";
    if ($('#idcliente').val() != '') {
        alert($('#idcliente').val());
        $.post(page, {pagina: pagina, paginacion: paginacion, idcliente: $('#idcliente').val()}, function (data) {
            $('#tblOrdenesVenta tbody').html(data);
        });
    }
}

/*Mantenimiento de producto
 -------------------------------------------------------------- */
function cargaSublinea1() {
    var idLinea = $('#lstLineaNuevo option:selected').val();
    var page = "/sublinea/listaroptions/" + idLinea;
    if (idLinea != 0) {
        $.post(page, function (data) {
            $('#lstSublineaNuevo').html('<option value="0">Seleccione Sublinea' + data);
        });
    }
}
function cargaSublinea2(idLinea) {
    var page = "/sublinea/listaroptions/" + idLinea;
    $.post(page, function (data) {
        $('#lstSublineaEditar').html(data);
    });
}
function existeCodigoProducto() {
    var codigoNuevo = $('#txtCodigoProducto').val();
    var codigoProducto = "";
    var ruta = "/producto/buscarcodigo/" + codigoNuevo;
    var exito = 1;
    $.ajax({
        async: false,
        url: ruta,
        type: "POST",
        dataType: "json",
        success: function (data) {
            codigoProducto = data.codigo;
        }
    });
    if (codigoNuevo == codigoProducto) {
        $.msgbox('Producto Nuevo', 'El c&oacute;digo de producto <strong>' + codigoNuevo + "</strong> ya existe.");
        return false;
    } else {
        return true;
    }
}
function verificaCodigoProducto() {
    var codigoNuevo = $('#txtCodigoProductoNuevo').val();
    var codigoProducto = "";
    var ruta = "/producto/buscarcodigo/" + codigoNuevo;
    var exito = 1;
    $.ajax({
        async: false,
        url: ruta,
        type: "POST",
        dataType: "json",
        success: function (data) {
            codigoProducto = data.codigo;
        }
    });
    if (codigoNuevo == codigoProducto) {
        $('#verificaCodigo').removeClass().addClass('incorrecto');
        $('#txtCodigoProductoNuevo').focus().select();
    } else {
        $('#verificaCodigo').removeClass().addClass('correcto');
    }
}
//validando la imagen
function verificaFormatoImagen() {
    extensionesPermitidas = new Array(".gif", ".jpg", ".png");
    imagen = $('#imagenProducto').val();
    if (imagen != "") {
        extension = (imagen.substring(imagen.lastIndexOf("."))).toLowerCase();
        permitida = false;
        for (var i = 0; i < extensionesPermitidas.length; i++) {
            if (extensionesPermitidas[i] == extension) {
                permitida = true;
                break;
            }
        }
        if (!permitida) {
            $.msgbox('Validaci&oacute;n de Imagen', 'La extensi&oacute;n de la imagen del producto es' +
                    'incorrecto.<br>S&oacute;lo se pueden subir imagenes con extensiones: <strong>' +
                    extensionesPermitidas.join() + "</strong>");
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}

/*Orden de compra
 -------------------------------------------------------------- */
//Funciones generales
function existeProducto(msgboxTitle) {
    var codigoProducto = $('#txtCodigoProducto').val();
    var idProducto = $('#txtIdProducto').val();
    var existe = 1;
    $.ajax({
        async: false,
        url: '/producto/existe/' + idProducto,
        type: "POST",
        dataType: "json",
        success: function (data) {
            existe = data.existe;
        }

    });
    if (existe == 0) {
        $.msgbox(msgboxTitle, 'El producto <strong>' + codigoProducto + "</strong> no existe.")
        return false;
    } else {
        return true;
    }
}
function existeProductoCompras(msgboxTitle) {
    var codigoProducto = $('#txtCodigoProductoCompras').val();
    var idProducto = $('#txtIdProducto').val();
    var existe = 1;
    $.ajax({
        async: false,
        url: '/producto/existe/' + idProducto,
        type: "POST",
        dataType: "json",
        success: function (data) {
            existe = data.existe;
        }

    });
    if (existe == 0) {
        $.msgbox(msgboxTitle, 'El producto <strong>' + codigoProducto + "</strong> no existe.")
        return false;
    } else {
        return true;
    }
}

/*Messagebox
 -------------------------------------------------------------- */
function execute() {
    var marginTop = "-" + ($("#msgbox").height() / 2) + "px";
    var marginLeft = "-" + ($("#msgbox").width() / 2) + "px";
    $("#msgbox").css({"margin-top": marginTop, "margin-left": marginLeft});
}


/* ordenventa y detalles */
function buscaOrdenVenta() {
    var ordenVenta = $('#txtIdOrden').val();
    var ruta = "/ordenventa/buscarfactura/" + ordenVenta;
    $.getJSON(ruta, function (data) {
        $('#txtCliente').val(data.cliente);
        $('#txtRucDni').val(data.rucdni);
        $('#txtFechaGuia').val(data.fechaguia);
        $('.inline-block input').exactWidth();
        if (data.porcentajefacturacion != 0) {
            $('#txtPorcentajeFacturacion').val(data.porcentajefacturacion);
            $('#lstModoFacturacion option[value="' + data.modofacturacion + '"]').attr('selected', true);
            $('.gbfacturacion').show();
        } else {
            $('.gbfacturacion').hide();
        }
    });
}

function cargaDetalleOrdenVenta() {
    var ordenVenta = $('#txtIdOrden').val();
    var ruta = "/facturacion/listaproductosguia/" + ordenVenta;
    $.post(ruta, function (data) {
        $('#tblProductosGuia tbody').html(data);
    });
}
//funcion para imprimir un parte de una pagina web
function imprSelec(muestra)
{
    //$('#contenedor').jqprint({ operaSupport: true });
    //$("#contenedor").printArea();

    $('#' + muestra + '').css('margin-right', '10px');
    $('#' + muestra + ' table').css('cellpadding', '0');
    $('#' + muestra + ' table').css('cellspacing', '0');
    $('#' + muestra + ' table').css('font-size', '5pts');
    $('#' + muestra + ' table').css('font-family', 'Arial');
    // $('#'+muestra+' table tbody th').css('background','#77C4E2');
    // $('#'+muestra+' table tbody td').css('background','#77C4E2');
    //style='border:5px solid #123456;'
    $('#' + muestra + '').jqprint({
        debug: false,
        importCSS: true,
        printContainer: true,
        operaSupport: true
    });
    $('#' + muestra + '').css('margin-right', '10px');

}
function completarIzquierda(n, length) {
    n = n.toString();
    while (n.length < length)
        n = "0" + n;
    return n;
}
// $( window ).bind( 'beforeunload', function() {
//    return "Estas tratando de cerrar/recargar esto puede causar la perdida/daño de información.!!!";
// } );
 