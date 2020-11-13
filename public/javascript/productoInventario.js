$(document).ready(function(){
    session_idrol=$('#txt_session_idrol').attr('value');
    var bueno=0;
	$('#txtProductoInventario').autocomplete({
		source: "/producto/buscarAutocompleteLimpio/",
		select: function(event, ui){
			$('#txtIdProducto').val(ui.item.id);
			//$('#codigoProducto').html(ui.item.value);
			$('#txtDescripcion').val(ui.item.tituloProducto);
			$('#btnAgregar').focus();
		}
	});
	$('#btnAgregar').click(function(e){
		e.preventDefault();
		agregarProducto();
      $("html, body").animate({ scrollTop: $(document).height() }, 100); 

	});

	$('.btnEliminar').live('click',function(e){
		e.preventDefault();
		if (confirm("Esta seguro de Eliminar este Producto")) {
			padre=$(this).parents('tr');
			padre.find('.estado').val(0);
			padre.hide();
		}		
	});

	$('.btnGrabar').live('click',function(e){
		e.preventDefault();
		padre=$(this).parents('tr');
//		if (confirm("Esta seguro de Grabar")) {
			var validacion=verificarBloque(padre);
			if (validacion==true) {
				grabarInventario(padre);
			}else{
				alert('El producto ya tiene un bloque asignado en este inventario\n el bloque es : '+validacion);
			}
//		}
 $("#txtProductoInventario").focus();
	});
        
        $('.btnSincronisar').live('click',function(){
		
		padre=$(this).parents('tr');
		if (confirm("Esta seguro de Grabar")) {
                    var idInventario=padre.find('.idInventario').val();
                   var  idBloque=padre.find('.idBloque').val();
                    var idproducto=padre.find('.id').val();
                   var  valorSincronisar=padre.find('.txtSincronisar').val();
                   
                    $.ajax({
                        async: false,
                        url: '/detalleinventario/sincronisarInventario',
                        type: 'get',
                        dataType: 'json',
                        data: {'idInventario':idInventario,'idBloque':idBloque,'idProducto':idproducto,'valorSincronisar': valorSincronisar},
                        success: function (resp) {
                            if (resp.exito==true) { 
                               alert("Guardado Correctamente");                   
                           }else{ 
                               alert("Errores al grabar");                   
                            }
                           
                       }
                    }); 
                    
		}		
	});
        
    	$('#lstCondicion').on("click change",function(e){
        e.preventDefault();
		e.preventDefault();
        condicionInventario=$(this).val();
        desabilitarCantidades();
        habilitaCantidades(condicionInventario);
        $('.observacion').removeAttr("readonly");
        $('.observacion').css('background','white');
	});

});

function desabilitarCantidades(){
                    $('.bueno').attr('readonly','readonly').css('background','silver');
                    $('.bueno2').attr('readonly','readonly').css('background','silver');
                    $('.bueno3').attr('readonly','readonly').css('background','silver');
                    $('.malo').attr('readonly','readonly').css('background','silver');
                    $('.servicio').attr('readonly','readonly').css('background','silver');
                    $('.vitrina').attr('readonly','readonly').css('background','silver');
}
function habilitaCantidades(condicionInventario){
    if(condicionInventario==1){
        $('.bueno').removeAttr("readonly");
        $('.bueno').css('background','white');
    }
    if(condicionInventario==2){
        $('.bueno2').removeAttr("readonly");
        $('.bueno2').css('background','white');
    }
    if(condicionInventario==3){
        $('.bueno3').removeAttr("readonly");
        $('.bueno3').css('background','white');
    }
    
    if(condicionInventario==4){
        $('.malo').removeAttr("readonly");
        $('.malo').css('background','white');
    }
    if(condicionInventario==5){
        $('.servicio').removeAttr("readonly");
        $('.servicio').css('background','white');
    }
    if(condicionInventario==6){
        $('.vitrina').removeAttr("readonly");
        $('.vitrina').css('background','white');
    }
}

function agregarProducto(){
	if ($('#txtIdProducto').val()!=0) {
		var vali=verificarBloqueAgregar();
		if (vali==true) {
			if (verificaExistencia()==true) {
				$('#tblProducto tbody').append(crearfila());
    			}else{
        		alert("El Producto ya fue agregado");
			}
		}else{
				alert('El producto ya tiene un bloque asignado en este inventario\n el bloque es : '+vali);
		}
	}else{
		alert("No ha Seleccionado ningun Producto");
	}
	
}

function verificaExistencia(){
	var idNuevo=$('#txtIdProducto').val();
	var verificacion=true;
	$('.id').each(function(){
		padre=$(this).parents('tr');
		id=padre.find('.id').val();
		estado=parseInt(padre.find('.estado').val());
		if (estado==1) {
			if (id==idNuevo) {
				verificacion=false;
			}
		}
	});
	return verificacion;
}
function crearfila(){
   idinventario= $('#lstInventario').val();
   idbloque= $('#lstBloques').val();
   idproducto= $('#txtIdProducto').val();

   fechaCierreInventario=$('#lstInventario option:selected').html().substr(19,12)
   
    $.ajax({
        async: false,
        url: '/detalleinventario/stockSegunKardex',
        type: 'get',
        dataType: 'json',
        data: {'idproducto': idproducto,'fechaCierre':fechaCierreInventario},
        success: function (resp) {
            get_stockKardex = resp.cantidad;
        }
    }); 
    
      $.ajax({
        async: false,
        url: '/detalleinventario/stockCierre',
        type: 'get',
        dataType: 'json',
        data: {'idinventario':idinventario,'idproducto': idproducto},
        success: function (resp) {
            get_stockCierre = resp.cantidad;
        }
    }); 
    
     $.ajax({
        async: false,
        url: '/detalleinventario/cnProductosdevoluciones',
        type: 'get',
        dataType: 'json',
        data: {'idproducto': idproducto,'fechaCierre':fechaCierreInventario},
        success: function (resp) {
           get_productosDevoluciones = resp.cantidad;
        }
    }); 
    
       $.ajax({
        async: false,
        url: '/detalleinventario/cnProductosSalidas',
        type: 'get',
        dataType: 'json',
        data: {'idproducto': idproducto,'fechaCierre':fechaCierreInventario},
        success: function (resp) {
            get_salidas = resp.cantidad;
        }
    }); 
      if(get_stockKardex== null || get_stockKardex =="undefined"){ get_stockKardex=0; }
      if(get_stockCierre== null || get_stockCierre =="undefined"){ get_stockCierre=0; }
      if(get_productosDevoluciones== null || get_productosDevoluciones =="undefined"){ get_productosDevoluciones=0; }
      if(get_salidas== null || get_salidas =="undefined"){ get_salidas=0; }
     get_stockKardex= parseInt(get_stockKardex);
     get_stockCierre= parseInt(get_stockCierre);
     get_productosDevoluciones= parseInt(get_productosDevoluciones);
     get_salidas= parseInt(get_salidas);
     
  $('#lblstockSegunKardex').html(get_stockKardex);   
  $('#lblstockCierre').html(get_stockCierre);   
  $('#lblcnProductosdevoluciones').html(get_productosDevoluciones);   
  $('#lblcnProductosSalidas').html(get_salidas);   
   $.ajax({
        async: false,
		url:'/detalleinventario/lista_cantidadesInventario_producto_bloque',
		type:'get',
		dataType:'json',
		data:{'idinventario':idinventario,'idbloque':idbloque,'idproducto':idproducto},
		success:function(resp){
            bueno=resp.buenos;
            bueno2=resp.buenos2;
            bueno3=resp.buenos3;
            malos=resp.malos;
            servicio=resp.serviciotecnico;
            showroom=resp.showroom;
            observacion=resp.observacion;
            if(bueno== null || bueno =="undefined"){ bueno=''; }
            if(bueno2== null || bueno2 =="undefined"){ bueno2=''; }
            if(bueno3== null || bueno3 =="undefined"){ bueno3=''; }
            if(malos== null || malos =="undefined"){ malos=''; }
            if(servicio== null || servicio =="undefined"){ servicio=''; }
            if(showroom== null || showroom =="undefined"){ showroom=''; }
            if(observacion== null || observacion =="undefined"){ observacion=''; }
            
        }
    });
    
        condicionInventario=$("#lstCondicion").val();
        if(condicionInventario==1){
            estilobueno="";
            propiedadbueno="";

            estilobueno2="background:silver;";
            propiedadbueno2="readonly='readonly'";
            
            estilobueno3="background:silver;";
            propiedadbueno3="readonly='readonly'";
            
            estilomalo="background:silver;";
            propiedadmalo="readonly='readonly'";
            
            estiloservicio="background:silver;";
            propiedadservicio="readonly='readonly'";
            
            estiloshowroom="background:silver;";
            propiedadshowroom="readonly='readonly'";
        }
        if(condicionInventario==2){
            estilobueno="background:silver;";
            propiedadbueno="readonly='readonly'";

            estilobueno2="";
            propiedadbueno2="";
            
            estilobueno3="background:silver;";
            propiedadbueno3="readonly='readonly'";
            
            estilomalo="background:silver;";
            propiedadmalo="readonly='readonly'";
            
            estiloservicio="background:silver;";
            propiedadservicio="readonly='readonly'";
            
            estiloshowroom="background:silver;";
            propiedadshowroom="readonly='readonly'";
        }
        if(condicionInventario==3){
             estilobueno="background:silver;";
            propiedadbueno="readonly='readonly'";

            estilobueno2="background:silver;";
            propiedadbueno2="readonly='readonly'";
            
            estilobueno3="";
            propiedadbueno3="";
            
            estilomalo="background:silver;";
            propiedadmalo="readonly='readonly'";
            
            estiloservicio="background:silver;";
            propiedadservicio="readonly='readonly'";
            
            estiloshowroom="background:silver;";
            propiedadshowroom="readonly='readonly'";
        }

    
        if(condicionInventario==4){
            estilobueno="background:silver;";
            propiedadbueno="readonly='readonly'";

            estilobueno2="background:silver;";
            propiedadbueno2="readonly='readonly'";
            
            estilobueno3="background:silver;";
            propiedadbueno3="readonly='readonly'";
            
            estilomalo="";
            propiedadmalo="";
            
            estiloservicio="background:silver;";
            propiedadservicio="readonly='readonly'";
            
            estiloshowroom="background:silver;";
            propiedadshowroom="readonly='readonly'";
        }
        if(condicionInventario==5){
            estilobueno="background:silver;";
            propiedadbueno="readonly='readonly'";

            estilobueno2="background:silver;";
            propiedadbueno2="readonly='readonly'";
            
            estilobueno3="background:silver;";
            propiedadbueno3="readonly='readonly'";
            
            estilomalo="background:silver;";
            propiedadmalo="readonly='readonly'";
            
            estiloservicio="";
            propiedadservicio="";
            
            estiloshowroom="background:silver;";
            propiedadshowroom="readonly='readonly'";
        }
        if(condicionInventario==6){
            estilobueno="background:silver;";
            propiedadbueno="readonly='readonly'";

            estilobueno2="background:silver;";
            propiedadbueno2="readonly='readonly'";
            
            estilobueno3="background:silver;";
            propiedadbueno3="readonly='readonly'";
            
            estilomalo="background:silver;";
            propiedadmalo="readonly='readonly'";
            
            estiloservicio="background:silver;";
            propiedadservicio="readonly='readonly'";
            
            estiloshowroom="";
            propiedadshowroom="";
        }
        if(session_idrol!=17){ 
        fila="<tr style='border-spacing:  0px 20px !important;'>"+
        "<td style='text-align:center;' rowspan='2'>"+$('#txtProductoInventario').val()+"<input class='id' type='hidden'  value='"+$('#txtIdProducto').val()+"'></td>"+
        "<td style='text-align:center;font-size:11px;' rowspan='2'>"+$('#txtDescripcion').val()+"</td>"+
        "<td style='text-align:center;font-size:10px;' rowspan='2'>"+$('#lstInventario option:selected').html().substr(0,13)+"<input type='hidden' class='idInventario' value='"+$('#lstInventario').val()+"' ></td>"+
        "<td style='text-align:center;'>"+$('#lstBloques option:selected').html()+"<input type='hidden' class='idBloque' value='"+$('#lstBloques').val()+"' ></td>"+
        "<td style='text-align:center;'><input "+propiedadbueno+" value='"+bueno+"' style='"+estilobueno+"width:50px !important;' required='required' type='text' class='numeric bueno'></td>"+
        "<td style='text-align:center;'><input "+propiedadbueno2+" value='"+bueno2+"' style='"+estilobueno2+"width:50px !important;' required='required' type='text' class='numeric bueno2'></td>"+
        "<td style='text-align:center;'><input "+propiedadbueno3+" value='"+bueno3+"' style='"+estilobueno3+"width:50px !important;' required='required' type='text' class='numeric bueno3'></td>"+
        "<td style='text-align:center;'><input "+propiedadmalo+" value='"+malos+"' style='"+estilomalo+"width:50px !important;' required='required' type='text' class='numeric malo' ></td>"+
        "<td style='text-align:center;'><input "+propiedadservicio+" value='"+servicio+"' style='"+estiloservicio+"width:50px !important;' required='required' type='text' class='numeric servicio' ></td>"+	
        "<td style='text-align:center;'><input "+propiedadshowroom+" value='"+showroom+"' style='"+estiloshowroom+"width:50px !important;' required='required' type='text' class='numeric vitrina' ></td>"+	
        "<td style='text-align:center;'><input placeholder='...' value='"+observacion+"' style='width:100px  type='text' class='observacion' ></td>"+	
//       "<td><input type='hidden' class='estado'  value='1' ><a href='' title='Eliminar'><img src='/imagenes/eliminar.gif' class='btnEliminar'></a> </td>"+
        "<td><input type='hidden' class='estado'  value='1' ><a href='' title='Eliminar'></td>"+		
        "<td rowspan='2'><a href='' title='Guardar'><img width='25' height='25' src='/imagenes/grabar.gif' class='btnGrabar c1_datashet'></a></td>"+
        "</tr>";
        fila+="<tr style='background: #c3dcfb;' class='abajito'>"+
        "<td style='text-align:center;' colspan='2'><input type='hidden' class='idInventario' value='"+$('#lstInventario').val()+"' /><input type='hidden' class='idBloque' value='"+$('#lstBloques').val()+"' /><input type='hidden' class='id' value='"+$('#txtIdProducto').val()+"' /><b>Kardex Cierre: </b> "+get_stockKardex+"</td>"+
        "<td style='text-align:center;' colspan='2'><b>Cierre: </b><input type='text' class='txtSincronisar' style='width:35px;' value='"+get_stockCierre+"'/><input type='button' class='btnSincronisar c2_datashet' value='UP'/></td>"+
        "<td style='text-align:center;' colspan='2'><b>Devoluciones: </b> "+get_productosDevoluciones+"</td>"+
        "<td style='text-align:center;' colspan='2'><b>Salidas: </b> "+get_salidas+"</td>"+
        //       "<td><input type='hidden' class='estado'  value='1' ><a href='' title='Eliminar'><img src='/imagenes/eliminar.gif' class='btnEliminar'></a> </td>"+
        "<td ></td></tr>";    
        }else{
        fila="<tr style='border-spacing:  0px 20px !important;'>"+
        "<td style='text-align:center;' >"+$('#txtProductoInventario').val()+"<input class='id' type='hidden'  value='"+$('#txtIdProducto').val()+"'></td>"+
        "<td style='text-align:center;font-size:11px;' >"+$('#txtDescripcion').val()+"</td>"+
        "<td style='text-align:center;font-size:10px;' >"+$('#lstInventario option:selected').html().substr(0,13)+"<input type='hidden' class='idInventario' value='"+$('#lstInventario').val()+"' ></td>"+
        "<td style='text-align:center;'>"+$('#lstBloques option:selected').html()+"<input type='hidden' class='idBloque' value='"+$('#lstBloques').val()+"' ></td>"+
        "<td style='text-align:center;'><input "+propiedadbueno+" value='"+bueno+"' style='"+estilobueno+"width:50px !important;' required='required' type='text' class='numeric bueno'></td>"+
        "<td style='text-align:center;'><input "+propiedadbueno2+" value='"+bueno2+"' style='"+estilobueno2+"width:50px !important;' required='required' type='text' class='numeric bueno2'></td>"+
        "<td style='text-align:center;'><input "+propiedadbueno3+" value='"+bueno3+"' style='"+estilobueno3+"width:50px !important;' required='required' type='text' class='numeric bueno3'></td>"+
        "<td style='text-align:center;'><input "+propiedadmalo+" value='"+malos+"' style='"+estilomalo+"width:50px !important;' required='required' type='text' class='numeric malo' ></td>"+
        "<td style='text-align:center;'><input "+propiedadservicio+" value='"+servicio+"' style='"+estiloservicio+"width:50px !important;' required='required' type='text' class='numeric servicio' ></td>"+	
        "<td style='text-align:center;'><input "+propiedadshowroom+" value='"+showroom+"' style='"+estiloshowroom+"width:50px !important;' required='required' type='text' class='numeric vitrina' ></td>"+	
        "<td style='text-align:center;'><input placeholder='...' value='"+observacion+"' style='width:100px  type='text' class='observacion' ></td>"+	
//       "<td><input type='hidden' class='estado'  value='1' ><a href='' title='Eliminar'><img src='/imagenes/eliminar.gif' class='btnEliminar'></a> </td>"+
        "<td><input type='hidden' class='estado'  value='1' ><a href='' title='Eliminar'></td>"+		
        "<td><a href='' title='Guardar'><img width='25' height='25' src='/imagenes/grabar.gif' class='btnGrabar c1_datashet'></a></td>"+
        "</tr>";            
        }
    

        $('#txtProductoInventario').val('');
        $('#txtDescripcion').val('');
        $('#txtIdProducto').val(0);
        return fila;   
}

function grabarInventario(padre){
	var idProducto=padre.find('.id').val();
	var productoVitrina=padre.find('.vitrina').val();
	var productoMalo=padre.find('.malo').val();
    var productoBueno=padre.find('.bueno').val();
    var productoBueno2=padre.find('.bueno2').val();
    var productoBueno3=padre.find('.bueno3').val();
    var productoObservacion=padre.find('.observacion').val();
    var estado=padre.find('.estado').val();
	var productoServicio=padre.find('.servicio').val();
	var idInventario=padre.find('.idInventario').val();
	var idBloque=padre.find('.idBloque').val();
     textInventario= $('#lstInventario option:selected').html().substr(0,13);
     condicionInventario= $('#lstCondicion').val();
  
    $.ajax({
		url:'/detalleinventario/grabarInventarioPart1',
		type:'post',
		dataType:'json',
		async:false,
		data:{'idProducto':idProducto,'productoVitrina':productoVitrina,'productoMalo':productoMalo,'productoServicio':productoServicio,'estado':estado,'idInventario':idInventario,'idBloque':idBloque,'productoBueno':productoBueno,'productoBueno2':productoBueno2,'productoBueno3':productoBueno3,'productoObservacion':productoObservacion,'textInventario':textInventario,'condicionInventario':condicionInventario},
		success:function(resp){
			console.log(resp);
			if (resp.exito==true) {
                alert("Grabado Correctamente");
//$("#lblRespuestaGrabado").html("Grabado Correctamente");
                padre.find('.vitrina').attr('readonly','readonly').css('background','silver');
				padre.find('.bueno').attr('readonly','readonly').css('background','silver');
				padre.find('.bueno2').attr('readonly','readonly').css('background','silver');
				padre.find('.bueno3').attr('readonly','readonly').css('background','silver');
				padre.find('.observacion').attr('readonly','readonly').css('background','silver');
                padre.find('.malo').attr('readonly','readonly').css('background','silver');
				padre.find('.servicio').attr('readonly','readonly').css('background','silver');
//				padre.find('.btnEliminar').hide();
//				padre.find('.btnGrabar').hide();
			}else{
				alert('Hubo un problema y no se pudo grabar');
			}
		}
	});
}

function verificarBloque(padre){
	var verificacion=true;
	var idProducto=padre.find('.id').val();
	var idInventario=padre.find('.idInventario').val();
	var idBloque=padre.find('.idBloque').val();
	$.ajax({
		url:'/detalleinventario/verificarBloque_para_que_no_se_duplique_en_bloques_de_un_mismo_inventario',
		type:'post',
		dataType:'json',
		async:false,
		data:{'idProducto':idProducto,'idInventario':idInventario,'idBloque':idBloque},
		success:function(resp){
			console.log(resp);
							verificacion=resp.exito;

		}
	});

	return verificacion;
}

function verificarBloqueAgregar(){
	var verificacion=true;
	var idProducto=$('#txtIdProducto').val();
	var idInventario=$('#lstInventario').val();
	var idBloque=$('#lstBloques').val();
	$.ajax({
		url:'/detalleinventario/verificarBloque_para_que_no_se_duplique_en_bloques_de_un_mismo_inventario',
		type:'post',
		dataType:'json',
		async:false,
		data:{'idProducto':idProducto,'idInventario':idInventario,'idBloque':idBloque},
		success:function(resp){
			console.log(resp);
				verificacion=resp.exito;
			
		}
	});

	return verificacion;
}

   