$(document).ready(function(){
	$('#btnBuscar').click(function(e){
		e.preventDefault();
		
			var idBloque=$('#lstBloques').val();
			var idInventario=$('#lstInventario').val();
			cargaBloque(idBloque,idInventario);
			$('#lstResponsable').attr('disabled','disabled');
			$('#lstAuxiliar').attr('disabled','disabled');
			$('#txtHoraInicio').attr('disabled','disabled');
			$('#txtHoraTermino').attr('disabled','disabled');
			//$('#lstInventario').attr('disabled','disabled');
			//$('#lstBloques').attr('disabled','disabled');
			//$(this).attr('disabled','disabled').html('Deshabilitado');
		
	});

	$('.buenos').live('blur',function(){
		padre=$(this).parents('tr');
		if ($(this).val()!='' && parseInt($(this).val())>=0) {
			buenos=parseInt(padre.find('.buenos').val());
			malos=parseInt(padre.find('.malos').val());
			servicio=parseInt(padre.find('.servicio').val());
			showroom=parseInt(padre.find('.showroom').val());
			total=buenos+malos+servicio+showroom;
			padre.find('.total').val(total);
		}else{
			alert('Ingrese un valor Correcto');
			$(this).focus();
		}
	});
        $('.btnEliminar').live('click',function(e){
            
            e.preventDefault();
            padre=$(this);
            id =$(this).parents('a').attr('rel');
            console.log(id);
            if (confirm("Esta seguro de Eliminar este Producto")) {
                        eliminarProductoBloque(id);
			padre.parents('tr').hide();
			
		   }	
      
            
        });
	$('.btnGrabar').live('click',function(e){
		e.preventDefault();
		if (confirm('Desea realmente Grabar')) {
			padre=$(this).parents('tr');
			idDetalleInventario=padre.find('.id').val();
			idProducto=parseInt(padre.find('.idProducto').val());
			buenos=parseInt(padre.find('.buenos').val());
			malos=parseInt(padre.find('.malos').val());
			servicio=parseInt(padre.find('.servicio').val());
			showroom=parseInt(padre.find('.showroom').val());
			responsable=$('#lstResponsable').val();
			auxiliar=$('#lstAuxiliar').val();
			horaInicio=$('#txtHoraInicio').val();
			horaTermino=$('#txtHoraTermino').val();
			guargaBuenos(padre,idDetalleInventario,idProducto,responsable,auxiliar,horaInicio,horaTermino,buenos,malos,servicio,showroom);
		}

	});

	$('#BtnNuevaBusqueda').click(function(e){
		e.preventDefault();
		$('#lstResponsable').removeAttr('disabled');
		$('#lstAuxiliar').removeAttr('disabled');
		$('#txtHoraInicio').removeAttr('disabled');
		$('#txtHoraTermino').removeAttr('disabled');
		$('#lstInventario').removeAttr('disabled');
		$('#lstBloques').removeAttr('disabled');
		$('#btnBuscar').removeAttr('disabled').html('Buscar');
		$('#tblInventario').html('');
	});
});

function cargaBloque(idBloque,idInventario){
	$.ajax({
		url:'/inventario/cargaBloque',
		type:'post',
		dataType:'html',
		data:{'idBloque':idBloque,'idInventario':idInventario},
		success:function(resp){
			$('#tblInventario').html('');
			$('#tblInventario').html(resp);
		}
	});
}
function guargaBuenos(padre,idDetalleInventario,idProducto,responsable,auxiliar,horaInicio,horaTermino,buenos,malos,servicio,showroom){
	$.ajax({
		url:'/detalleinventario/guardaBuenos',
		type:'post',
		dataType:'json',
		data:{'idDetalleInventario':idDetalleInventario,'idProducto':idProducto,'responsable':responsable,'auxiliar':auxiliar,'horaInicio':horaInicio,'horaTermino':horaTermino,'buenos':buenos,'malos':malos,'servicio':servicio,'showroom':showroom},
		success:function(resp){
			console.log(resp);
			if (resp.verificacion==true) {
				padre.find('.btnGrabar').hide();
				padre.find('.buenos').attr('readonly','readonly').css('background','silver');
				alert('Se grabo Correctamente');
			}else{
				alert('No se pudo Guardar hubo algun problema');
			}
		}
	});
}
function  eliminarProductoBloque(id){
    $.ajax({
               url:'/detalleinventario/eliminar',
               type:'post',
               dattype:'json',
               data:{'iddetalleinventario':id},
               success:function(resp){
                   console.log(resp) ;                  	
               
               }
           });
}