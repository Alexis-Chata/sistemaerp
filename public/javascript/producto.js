$(document).on("ready", function () {
  var validacion = false;
  var valcodigo = "";
  var lista = 0;
  $("#btnPrueba").on("click", function (e) {
    e.preventDefault();
    var nombre = $("#txtNombre").val();
    $.ajax({
      type: "POST",
      url: "/producto/listar2/",
      dataType: "json",
      data: { nombre: nombre },
      success: function (result) {
        $("#grid").data("kendoGrid").dataSource.data(result);
      },
    });
  });
  $("#validaPrecios").click(function () {
    if ($(this).attr("checked") == "checked") {
      $("#precioLista")
        .removeClass("required")
        .attr("disabled", "disabled")
        .val("");
      $("#precioCosto")
        .removeClass("required")
        .attr("disabled", "disabled")
        .val("");
    } else {
      $("#precioLista").addClass("required").removeAttr("disabled");
      $("#precioCosto").addClass("required").removeAttr("disabled");
    }
  });

  $("#txtNombre").on("keyup", function () {
    if ($(this).val() != "") {
      tweetDS.read();
    }
  });

  $("#btnCancelar").on("click", function (e) {
    e.preventDefault();
    window.location = "/producto/lista/";
  });

  /*Autocompletes*/
  //Autocomplete Producto Repuesto
  $("#txtCodigoProductoRepuesto").autocomplete({
    minLength: 2,
    source: function (request, response) {
      $.ajax({
        url: "/producto/buscarAutocompleteRepuesto/",
        dataType: "json",
        data: {
          term: request.term,
          idlinea: $("#lstLinea option:selected").val(),
        },
        success: function (data) {
          console.log(data);
          response(data);
        },
      });
    },
    select: function (event, ui) {
      //console.log(ui);
      $("#repuestoproducto fieldset li").each(function (index) {
        if (index >= 2) {
          $(this).remove();
        }
      });
      $("#txtIdProductoRepuesto").val(ui.item.id);
      $("#txtTituloProductoRepuesto").val(ui.item.tituloProducto);
      $("#codigopaRepuesto").val(ui.item.value);
      $("#imgimgProductos").attr('src','/imagenes/productos/'+ui.item.value+'/'+ui.item.imagen);
      $('#fieldsetRepuesto ul li').last().remove()
      $('#fieldsetRepuesto ul').append(
        $("<li>", {
          style: "text-align: center;",
        }).append(
          $("<input>", {
            id:'btnEnviar',
            class: "button",
            type: "submit",
            value: "Registrar",
          })
        ).append(
          $("<button>", {
            id:'btnCancelar',
            class: "button",
            text: "Cancelar",
          })
        )
      );
      if(ui.item.productospadre){
        $.each(ui.item.productospadre, function (index, value) {
          //console.log(value.nompro);
          $("#txtIdProducto").val(value.idproducto);
          $("#txtTituloProducto").val(value.nompro);
          
          $("#labelDescripcion").width();
          $("#produc").after(
            $("<li>")
              .append(
                $("<label>", {
                  id:'labelDescripcion',
                  class: "mr_4",
                  style: "width: "+$("#labelDescripcion").width()+"px;",
                  text: "Descripcion",
                })
              )
              .append(
                $("<input>", {
                  type: "text",
                  id: "txtTituloProducto",
                  disabled: true,
                  style: "width: 300px;",
                })
              )
              .append(
                $("<input>", {
                  type: "hidden",
                  name: "productospadres[]",
                  id: "txtIdProducto",
                })
              )
          );
          $("ul fieldset li:nth-child(3)").append(
            $("<span>", {
              class: "ui-icon ui-icon-closethick",
              style: "cursor: pointer;margin-left: 3px;",
              text: "close",
            }).on("click", function () {
              $(this).parent()[0].remove();
              $("#txtCodigoProducto").val("");
              if (
                document.getElementsByName("productospadres[]").valueOf()[
                  "length"
                ] <= 1
              ) {
                $("#txtCodigoProducto").addClass("required");
              }
            })
          );
          $("#txtCodigoProducto").removeClass("required");
        });
      }
      
    },
  });

  /*Autocompletes*/
  //Autocomplete Producto
  $("#txtCodigoProducto").autocomplete({
    minLength: 2,
    source: function (request, response) {
      $.ajax({
        url: "/producto/buscarautocomplete/",
        dataType: "json",
        data: {
          term: request.term,
          idlinea: $("#lstLinea option:selected").val(),
        },
        success: function (data) {
          response(data);
        },
      });
    },
    select: function (event, ui) {
      //console.log(ui);
      $("#txtIdProducto").val(ui.item.id);
      $("#txtTituloProducto").val(ui.item.tituloProducto);
      $("#produc").after(
        $("<li>")
          .append(
            $("<label>", {
              class: "mr_4",
              style: "width: 138px;",
              text: "Descripcion",
            })
          )
          .append(
            $("<input>", {
              type: "text",
              id: "txtTituloProducto",
              disabled: true,
              style: "width: 300px;",
            })
          )
          .append(
            $("<input>", {
              type: "hidden",
              name: "productospadres[]",
              id: "txtIdProducto",
            })
          )
      );
      $("ul fieldset li:nth-child(3)").append(
        $("<span>", {
          class: "ui-icon ui-icon-closethick",
          style: "cursor: pointer;",
          text: "close",
        }).on("click", function () {
          $(this).parent()[0].remove();
          $("#txtCodigoProducto").val("");
          if (
            document.getElementsByName("productospadres[]").valueOf()[
              "length"
            ] <= 1
          ) {
            $("#txtCodigoProducto").addClass("required");
          }
        })
      );
      $("#txtCodigoProducto").removeClass("required");
    },
  });

  $("ul fieldset li span").on("click", function () {
    $(this).parent()[0].remove();
    $("#txtCodigoProducto").val("");
    if (
      document.getElementsByName("productospadres[]").valueOf()["length"] <= 1
    ) {
      $("#txtCodigoProducto").addClass("required");
    }
  });

  $("#repuestosi").on("click", function repuestosi() {
    $("#repuestoproducto").removeClass("hide");
    $("#txtCodigoProducto").addClass("required");
    $('select[name="Producto[idlinea]"]').removeAttr("id");
    $("#registroProducto select").each(function (index, value) {
      value.classList.remove("required");
    });
    $('select[name="Producto[idlinea]"]').removeAttr("required");
    $("#registroProducto select").each(function (index, value) {
      value.classList.add("required");
    });
    $('select[name="Producto[idalmacen]"]')[0].value = null;
    $('select[name="Producto[idlinea]"]')[0].value = null;
    $("#registroProducto").hide();
  });

  $("#repuestono").on("click", function () {
    document.getElementById("txtCodigoProducto").value = null;
    document.getElementById("txtTituloProducto").value = null;
    document.getElementById("txtIdProducto").value = null;
    $("#repuestoproducto fieldset li").each(function (index) {
      if (index >= 2) {
        $(this).remove();
      }
    });
    $("#txtCodigoProducto").removeClass("required");
    $("#repuestoproducto").addClass("hide");
    $('select[name="Producto[idlinea]"]').attr("id", "lstSublineaNuevo");
    $("#registroProducto select").each(function (index, value) {
      value.classList.add("required");
    });
    $("#registroProducto").show();
  });

  /*Boton de eliminacion*/
  $(".btnEliminar").on("click", function (e) {
    e.preventDefault();
    var url = $(this).attr("href");
    $.msgbox(msgboxTitleAlmacen, "¿Esta seguro de elimiar el registro?");
    $("#msgbox-ok").click(function () {
      window.location = url;
    });
  });

  $("#imprimir").click(function (e) {
    e.preventDefault();
    imprSelec("contenedorImpresion");
  });

  /* Lista de busqueda*/
  $("#seleccion").change(function () {
    var id = $("#seleccion option:selected").text();
    var url = "/producto/lista/" + id;
    window.location = url;
  });

  $("#nuevoAlmacen").on("click", function (e) {
    e.preventDefault();

    window.location = "/almacen/nuevo";
  });

  $("#nuevoEmpaque").on("click", function (e) {
    e.preventDefault();
    window.location = "/empaque/nuevo";
  });
  $("#nuevaSublinea").on("click", function (e) {
    e.preventDefault();
    window.location = "/sublinea/nuevo/";
  });

  $("#nuevaMarca").on("click", function (e) {
    e.preventDefault();
    window.location = "/marca/nuevo/";
  });

  $("#nuevoProveedor").on("click", function (e) {
    e.preventDefault();
    window.location = "/proveedor/nuevo";
  });

  $("#verificaCodigo").on("click", function (e) {
    e.preventDefault();
    $.ajax({
      url: "/producto/validarCodigo/",
      type: "POST",
      dataType: "json",
      data: $("#frmProductoNuevo").serialize(),
      success: function (respuesta) {
        //console.log(respuesta);
        $("#error").text(respuesta.error);
        validacion = respuesta.verificado;
        valcodigo = respuesta.codigopa;
        if (validacion == true && valcodigo != "") {
          $("#error").css({ color: "green" });
          $("#codigoFabrica").attr("readonly", "readonly");
          $("#lstSublineaNuevo").attr("required", "required");
        } else {
          $("#error").css({ color: "red" });
        }
      },
      error: function (jqXHR, status, error) {
        //console.log(estado)
        //console.log(error)
      },
    });
  });

  $("#btnEnviar").on("click", function (e) {
    lista = $("#lstSublineaNuevo").attr("value");

    if ($('#codigopaRepuesto').length) {
      if($('#codigopaRepuesto')[0].value==""){
        e.preventDefault();
        $("#mensajeModal").dialog("open");
      }
    } else if (validacion == false) {
      e.preventDefault();
      $("#mensajeModal").dialog("open");
    } else if (lista == 0) {
      $("#mensajeModal").text("Seleccione una Sublinea");
      $("#mensajeModal").dialog("open");
      e.preventDefault();
    }
  });
  $("#mensajeModal").dialog({
    autoOpen: false,
    modal: true,
    width: 300,
  });

  $("#btnLimpiar").on("click", function (e) {
    $("#codigoFabrica").removeAttr("readonly");
    $("#lstSublineaNuevo").removeAttr("required");
    $("#error").text("");
    validacion = false;
  });

  /**/

  /***************************** Ventana Modal para Linea *****************************************/
  var nombreLinea = $("#nombreLinea"),
    allLineas = $([]).add(nombreLinea),
    tips = $(".validateTips");

  $("#nuevaLinea").dialog({
    autoOpen: false,
    show: "blind",
    resizable: false,
    height: 300,
    width: "auto",
    modal: true,
    buttons: {
      Enviar: function () {
        var bValid = true;
        allLineas.removeClass("ui-state-error");

        bValid = bValid && checkLength(nombreLinea, "nombre", 3, 50);
        if (bValid) {
          $.ajax({
            url: "/linea/grabaJason/",
            type: "POST",
            dataType: "json",
            data: { nomlin: nombreLinea.val() },
            success: function (respuesta) {
              if (respuesta.valid) {
                $("#respLinea").text(respuesta.resp);
                $("#respLinea").css({ color: "green" });
                $("#lstLineaNuevo").append(
                  "<option value=" +
                    respuesta.idlinea +
                    ">" +
                    nombreLinea.val() +
                    "</option>"
                );
                $("#lstLineaModal").append(
                  "<option value=" +
                    respuesta.idlinea +
                    ">" +
                    nombreLinea.val() +
                    "</option>"
                );
                allLineas.val("");
              } else {
                $("#respLinea").text(respuesta.resp);
                $("#respLinea").css({ color: "red" });
              }
            },
            error: function (jqXHR, status, error) {
              $("#respLinea").text("Error al intentar enviar peticion");
            },
          });
        }
      },
      Cancelar: function () {
        allLineas.val("").removeClass("ui-state-error");
        tips.text("Todos los campos son requeridos.");
        $("#respLinea").text("");
        $(this).dialog("close");
        //location.reload();
      },
    },
    close: function () {
      allLineas.val("").removeClass("ui-state-error");
      tips.text("Todos los campos son requeridos.");
      $("#respLinea").text("");
    },
  });

  $("#agregarLinea")
    .button()
    .click(function () {
      $("#nuevaLinea").dialog("open");
    });

  /********************************* Validacion General *******************************************/
  function updateTips(t) {
    tips.text(t).addClass("ui-state-highlight");
    setTimeout(function () {
      tips.removeClass("ui-state-highlight", 1500);
    }, 500);
  }

  function checkLength(o, n, min, max) {
    if (o.val().length > max) {
      o.addClass("ui-state-error");
      updateTips("El Maximo Tamaño de " + n + " es: " + max + ".");
      return false;
    } else if (o.val().length < min) {
      o.addClass("ui-state-error");
      updateTips("El Minimo Tamaño de " + n + " es: " + min + ".");
      return false;
    } else {
      return true;
    }
  }

  function checklista(lista, nombre) {
    if (lista.val() == 0) {
      lista.addClass("ui-state-error");
      updateTips("No ha seleccionado ningun " + nombre);
      return false;
    } else {
      return true;
    }
  }

  function checkRegexp(o, regexp, n) {
    if (!regexp.test(o.val())) {
      o.addClass("ui-state-error");
      updateTips(n);
      return false;
    } else {
      return true;
    }
  }

  /**************************** Ventana Modal para Almacen ****************************************/

  var nombreAlmacen = $("#nombreAlmacen"),
    razonSocialAlmacen = $("#razonSocialAlmacen"),
    direccionAlmacen = $("#direccionAlmacen"),
    rucAlmacen = $("#rucAlmacen"),
    allAlmacenes = $([])
      .add(nombreAlmacen)
      .add(razonSocialAlmacen)
      .add(direccionAlmacen)
      .add(rucAlmacen),
    tips = $(".validateTips");

  $("#nuevoAlmacenPrueba").dialog({
    autoOpen: false,
    height: 400,
    width: 400,
    modal: true,
    buttons: {
      enviar: function () {
        var bValid = true;
        allAlmacenes.removeClass("ui-state-error");

        bValid = bValid && checkLength(nombreAlmacen, "nombre", 3, 100);
        bValid =
          bValid && checkLength(razonSocialAlmacen, "Razon Social", 3, 100);
        bValid = bValid && checkLength(direccionAlmacen, "Direccion", 3, 100);
        bValid = bValid && checkLength(rucAlmacen, "RUC", 11, 11);

        bValid =
          bValid &&
          checkRegexp(rucAlmacen, /^([0-9])+$/, "Solo Puede ingresar digitos.");

        if (bValid) {
          $.ajax({
            url: "/almacen/grabaJason/",
            type: "POST",
            dataType: "json",
            data: {
              nomalm: nombreAlmacen.val(),
              razsocalm: razonSocialAlmacen.val(),
              diralm: direccionAlmacen.val(),
              rucalm: rucAlmacen.val(),
            },
            success: function (respuestaAlmacen) {
              if (respuestaAlmacen.valid) {
                $("#respAlmacen").text(respuestaAlmacen.resp);
                $("#respAlmacen").css({ color: "green" });
                $("#lstAlmacen").append(
                  "<option value=" +
                    respuestaAlmacen.idAlmacen +
                    ">" +
                    nombreAlmacen.val() +
                    "</option>"
                );
                allAlmacenes.val("");
              } else {
                $("#respAlmacen").text(respuestaAlmacen.resp);
                $("#respAlmacen").css({ color: "red" });
              }
            },
            error: function (jqXHR, status, error) {
              $("#respAlmacen").text("Error al intentar enviar peticion");
            },
          });
        }
      },
      Cancelar: function () {
        allAlmacenes.val("").removeClass("ui-state-error");
        tips.text("Todos los campos son requeridos.");
        $("#respAlmacen").text("");
        $(this).dialog("close");
      },
    },
    close: function () {
      allAlmacenes.val("").removeClass("ui-state-error");
      tips.text("Todos los campos son requeridos.");
      $("#respAlmacen").text("");
    },
  });

  $("#agregarAlmacen")
    .button()
    .click(function () {
      $("#nuevoAlmacenPrueba").dialog("open");
    });

  /********************************* Ventana Modal para Proveedor *********************************/
  var razsocProveedor = $("#razsocProveedor"),
    repreProveedor = $("#repreProveedor"),
    percontactoProveedor = $("#percontactoProveedor"),
    direccionProveedor = $("#direccionProveedor"),
    descripcionProveedor = $("#descripcionProveedor"),
    rucProveedor = $("#rucProveedor"),
    emailPrincipalProveedor = $("#emailPrincipalProveedor"),
    emailAltenativoProveedor = $("#emailAltenativoProveedor"),
    paginaProveedor = $("#paginaProveedor"),
    telefonoprincipalProveedor = $("#telefonoprincipalProveedor"),
    telefonoalternativoProveedor = $("#telefonoalternativoProveedor"),
    faxProveedor = $("#faxProveedor"),
    allProveedor = $([])
      .add(razsocProveedor)
      .add(repreProveedor)
      .add(percontactoProveedor)
      .add(direccionProveedor)
      .add(descripcionProveedor)
      .add(rucProveedor)
      .add(emailPrincipalProveedor)
      .add(emailAltenativoProveedor)
      .add(paginaProveedor)
      .add(telefonoprincipalProveedor)
      .add(telefonoalternativoProveedor)
      .add(faxProveedor),
    tips = $(".validateTips");

  $("#nuevoProveedorModal").dialog({
    autoOpen: false,
    modal: true,
    width: 400,
    height: "auto",
    buttons: {
      enviar: function () {
        var bValid = true;
        allProveedor.removeClass("ui-state-error");

        bValid = bValid && checkLength(razsocProveedor, "Razon Social", 3, 100);
        bValid =
          bValid && checkLength(repreProveedor, "Representante Legal", 0, 200);
        bValid =
          bValid &&
          checkLength(percontactoProveedor, "Persona Contacto", 0, 200);
        bValid = bValid && checkLength(direccionProveedor, "Direccion", 0, 100);
        bValid =
          bValid && checkLength(descripcionProveedor, "Descripcion", 0, 150);

        bValid = bValid && checkLength(rucProveedor, "RUC", 0, 11);

        bValid =
          bValid &&
          checkLength(emailPrincipalProveedor, "E-mail Principal", 0, 100);
        bValid =
          bValid &&
          checkRegexp(
            emailPrincipalProveedor,
            /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
            "Formato Incorrecto del Correo"
          );

        bValid =
          bValid &&
          checkLength(emailAltenativoProveedor, "E.mail Alternativo", 0, 200);
        bValid =
          bValid &&
          checkRegexp(
            emailAltenativoProveedor,
            /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
            "Formato Incorrecto del Correo"
          );

        bValid = bValid && checkLength(paginaProveedor, "Pagina Web", 0, 200);
        bValid =
          bValid &&
          checkLength(telefonoprincipalProveedor, "Telefono Principal", 0, 11);
        bValid =
          bValid &&
          checkLength(
            telefonoalternativoProveedor,
            "Telefono Alternativo",
            0,
            11
          );
        bValid = bValid && checkLength(faxProveedor, "Fax", 0, 12);

        if (bValid) {
          $.ajax({
            url: "/proveedor/grabaJason/",
            type: "POST",
            dataType: "json",
            data: $("#frmProveedor").serialize(),
            success: function (respuestaProveedor) {
              if (respuestaProveedor.valid) {
                $("#respProveedor").text(respuestaProveedor.resp);
                $("#respProveedor").css({ color: "green" });
                $("#lstProveedor").append(
                  "<option value=" +
                    respuestaProveedor.idProveedor +
                    ">" +
                    razsocProveedor.val() +
                    "</option>"
                );
                allProveedor.val("");
              } else {
                $("#respProveedor").text(respuestaProveedor.resp);
                $("#respProveedor").css({ color: "red" });
              }
            },
            error: function (jqXHR, status, error) {
              $("#respProveedor").text("Error al intentar enviar peticion");
            },
          });
        }
      },
      Cancelar: function () {
        allProveedor.val("").removeClass("ui-state-error");
        tips.text("Todos los campos son requeridos.");
        $("#respProveedor").text("");
        $(this).dialog("close");
      },
    },
    close: function () {
      allProveedor.val("").removeClass("ui-state-error");
      tips.text("Todos los campos son requeridos.");
      $("#respProveedor").text("");
    },
  });

  $("#agregarProveedor")
    .button()
    .click(function () {
      $("#nuevoProveedorModal").dialog("open");
    });

  /********************************** Ventana Modal para sublinea *********************************/
  var lstLineaModal = $("#lstLineaModal"),
    nombreSublinea = $("#nombreSublinea"),
    allSublinea = $([]).add(lstLineaModal).add(nombreSublinea),
    tips = $(".validateTips");

  $("#nuevaSublineaModal").dialog({
    autoOpen: false,
    height: "400",
    width: "auto",
    modal: true,
    buttons: {
      enviar: function () {
        var bValid = true;
        allAlmacenes.removeClass("ui-state-error");

        bValid = bValid && checklista(lstLineaModal, "Linea");
        bValid = bValid && checkLength(nombreSublinea, "Nombre", 3, 50);

        if (bValid) {
          $.ajax({
            url: "/sublinea/grabaJason/",
            type: "POST",
            dataType: "json",
            data: {
              idpadre: lstLineaModal.val(),
              nomlin: nombreSublinea.val(),
            },
            success: function (respuestaSublinea) {
              if (respuestaSublinea.valid) {
                $("#respSublinea").text(respuestaSublinea.resp);
                $("#respSublinea").css({ color: "green" });
                allAlmacenes.val("");
              } else {
                $("#respSublinea").text(respuestaSublinea.resp);
                $("#respSublinea").css({ color: "red" });
              }
            },
            error: function (jqXHR, status, error) {
              $("#respSublinea").text("Error al intentar enviar peticion");
            },
          });
        }
      },
      cerrar: function () {
        allSublinea.val("").removeClass("ui-state-error");
        tips.text("Todos los campos son requeridos.");
        $("#respSublinea").text("");
        $(this).dialog("close");
        location.reload();
      },
    },
    close: function () {
      allSublinea.val("").removeClass("ui-state-error");
      tips.text("Todos los campos son requeridos.");
      $("#respSublinea").text("");
      location.reload();
    },
  });

  $("#agregarSublinea")
    .button()
    .click(function () {
      $("#nuevaSublineaModal").dialog("open");
    });

  /************************************ Ventana modal para Marca **********************************/
  var nombreMarca = $("#nombreMarca"),
    allMarca = $([]).add(nombreMarca),
    tips = $(".validateTips");

  $("#nuevaMarcaModal").dialog({
    autoOpen: false,
    show: "blind",
    resizable: false,
    height: 300,
    width: "auto",
    modal: true,
    buttons: {
      Enviar: function () {
        var bValid = true;
        allLineas.removeClass("ui-state-error");

        bValid = bValid && checkLength(nombreMarca, "nombre", 3, 50);
        if (bValid) {
          $.ajax({
            url: "/marca/grabaJason/",
            type: "POST",
            dataType: "json",
            data: { nombre: nombreMarca.val() },
            success: function (respuesta) {
              if (respuesta.valid) {
                $("#respMarca").text(respuesta.resp);
                $("#respMarca").css({ color: "green" });
                $("#lstMarca").append(
                  "<option value=" +
                    respuesta.idMarca +
                    ">" +
                    nombreMarca.val() +
                    "</option>"
                );
                allMarca.val("");
              } else {
                $("#respMarca").text(respuesta.resp);
                $("#respMarca").css({ color: "red" });
              }
            },
            error: function (jqXHR, status, error) {
              $("#respMarca").text("Error al intentar enviar peticion");
            },
          });
        }
      },
      Cancelar: function () {
        allMarca.val("").removeClass("ui-state-error");
        tips.text("Todos los campos son requeridos.");
        $("#respMarca").text("");
        $(this).dialog("close");
        //location.reload();
      },
    },
    close: function () {
      allMarca.val("").removeClass("ui-state-error");
      tips.text("Todos los campos son requeridos.");
      $("#respMarca").text("");
    },
  });

  $("#agregarMarca")
    .button()
    .click(function () {
      $("#nuevaMarcaModal").dialog("open");
    });
  /************************************ Ventana modal para Unidad **********************************/
  var nombreUnidadMedida = $("#nombreUnidad"),
    codigoUnidadMedida = $("#codigoUnidad"),
    allUnidad = $([]).add(nombreUnidadMedida).add(codigoUnidadMedida),
    tips = $(".validateTips");

  $("#nuevaUnidadModal").dialog({
    autoOpen: false,
    show: "blind",
    resizable: false,
    height: 300,
    width: "auto",
    modal: true,
    buttons: {
      Enviar: function () {
        var bValid = true;
        allLineas.removeClass("ui-state-error");

        bValid = bValid && checkLength(nombreUnidadMedida, "nombre", 3, 20);
        bValid = bValid && checkLength(codigoUnidadMedida, "codigo", 3, 20);
        if (bValid) {
          $.ajax({
            url: "/unidadmedida/grabaJason/",
            type: "POST",
            dataType: "json",
            data: {
              nombre: nombreUnidadMedida.val(),
              codigo: codigoUnidadMedida.val(),
            },
            success: function (respuesta) {
              if (respuesta.valid) {
                $("#respUnidad").text(respuesta.resp);
                $("#respUnidad").css({ color: "green" });
                $("#lstUnidadMedida").append(
                  "<option value=" +
                    respuesta.idUnidadMedida +
                    ">" +
                    nombreUnidadMedida.val() +
                    "</option>"
                );
                allUnidad.val("");
              } else {
                $("#respUnidad").text(respuesta.resp);
                $("#respUnidad").css({ color: "red" });
              }
            },
            error: function (jqXHR, status, error) {
              $("#respUnidad").text("Error al intentar enviar peticion");
            },
          });
        }
      },
      Cancelar: function () {
        allUnidad.val("").removeClass("ui-state-error");
        tips.text("Todos los campos son requeridos.");
        $("#respUnidad").text("");
        $(this).dialog("close");
        //location.reload();
      },
    },
    close: function () {
      allUnidad.val("").removeClass("ui-state-error");
      tips.text("Todos los campos son requeridos.");
      $("#respUnidad").text("");
    },
  });

  $("#agregarUnidad")
    .button()
    .click(function () {
      $("#nuevaUnidadModal").dialog("open");
    });

  /************************************************************************************************/

  $("#imprimirProducto").click(function (e) {
    e.preventDefault();
    imprSelec("modal");
  });
  $("#contenedorModal").css("overflow", "auto").dialog({
    title: "GALERIA DE IMAGENES DATASHETT !!",
    autoOpen: false,
    modal: true,
    width: 900,
    height: 650,
    resizable: false,
    draggable: true,
  });

  $("#imgProductos").on("click", function () {
    console.log("hola");
    $("#contenedorModal").dialog("open");
  });
});

function getData() {
  var _resData;
  var nombre = $("#txtNombre").val();
  $.ajax({
    type: "POST",
    url: "/producto/listar2/",
    dataType: "json",
    data: { nombre: nombre },
    success: function (data) {
      _resData = data;
    },
    async: false,
  });
  return _resData;
}
