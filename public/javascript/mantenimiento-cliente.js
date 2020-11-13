$(document).ready(function(){
	/*Muestra el Cuadro de dialogo luego agrega nuevo transporte para el cliente*/
	var idclientesucursal;
	var padregeneral;
        
        $('.pagCliente').click(function () {
            var page = $(this).data('pag');
            $('#frmBusquedaCliente').attr('action', '/cliente/busca/' + page);
            $('#frmBusquedaCliente').submit();
            return false;
        });

	$('#btnNuevoTransporte').click(function(e){
		e.preventDefault();
		nuevoTransporte('btnGuardaTransporte');
	});
	$('#btnAgregaTransporte').click(function(e){
		e.preventDefault();
		nuevoTransporte('btnAgregarTransporte');
	});
	$('#btnCancelar').on('click', function(e){
		e.preventDefault();
		window.location = "/cliente/lista/";
	});



	/****************/
	$('#lstbusqueda').change(function(){
		var id=$(this).val();
		$('#txtBusqueda').attr('value',id);
	});

	$("#seleccion").change(function(){
		var id=$("#seleccion option:selected").text();
		var url='/cliente/lista/'+id;
		window.location=url;
	});

	$('#btnCambiarTransporte').click(function(e){
		e.preventDefault();
		$('#lstTranspo').removeAttr('disabled');
		$('#lstTransporte').attr('required','required');
		$('#lstTranspo').attr('required','required');
	});

	$('#btnCancelarCambio').click(function(e){
		e.preventDefault();
		$('#lstTranspo').attr('disabled','disabled');
		$('#lstTransporte').removeAttr('required');
		$('#lstTranspo').removeAttr('required');
		$("#lstTransporte option:first").attr('selected',true);
		$("#lstTranspo option:first").attr('selected',true);
	});

	$('#codigoCliente').keyup(function(){
		codigo=$('#codigoCliente').attr('value');
		
		$.ajax({
			url: '/cliente/validarCodigo/',
			type: 'POST',
			dataType:'json',
			data:{'codigo':codigo},
			success: function(respuesta){
				//console.log(respuesta)
				
				validacion=respuesta.verificado;
				
				if (validacion==true && codigo>0) {
					$('#error').css({'color':'green'});
					$('#error').text(respuesta.error);
					//$('#nuevoCliente').removeAttr('disabled');
					$('#nuevoCliente').removeAttr('title');
					
				}else{
					$('#error').css({'color':'red'})
					$('#error').text(respuesta.error);
					//$('#nuevoCliente').attr('disabled','disabled');
					$('#nuevoCliente').attr('title','Estoy Deshabilitado');
				};

			},
			error: function(jqXHR, status, error) {
			//console.log(status)
			//console.log(error)
			}
		});
	});

	//contacto
	$('#contenedor').dialog({
		width:720,
		height:560,
		rezisable:false,
		autoOpen:false,
		modal:true,
		close:function(){
			$('#frmNuevo')[0].reset();
		}
	});

	$('#botonFacturacion').button().live('click',function(e){
		e.preventDefault();
		idContenedor=$(this).find('.idsucursal').val();
		$('#dataFacturacion').show();
		$('#dataDespacho').hide();
		
	});
	$('#botonDespacho').button().live('click',function(e){
		e.preventDefault();
		idContenedor=$(this).find('.idsucursal').val();
		$('#dataFacturacion').hide();
		$('#dataDespacho').show();
		

	});
	$('.btnEliminarSucursal').live('click',function(e){
		//alert('entro');
		e.preventDefault();
		var padre=$(this).parents('tr');
		id=padre.find('.idclientesucursal').val();
		eliminarSucursal(id,padre);
		
	});
	$('.btnEditarSucursal').live('click',function(e){
		e.preventDefault();
		padregeneral=$(this).parents('tr');
		idclientesucursal=padregeneral.find('.idclientesucursal').val();
		$('#btnActualizar').show();
		$('#btnGrabar').hide();
		cargaSucursal(idclientesucursal);
	});
	$('#btnNuevo').click(function(e){
		e.preventDefault();
		var padre=$(this).parents('tr');
		$('#btnActualizar').hide();
		$('#btnGrabar').show();
		$('#contenedor').dialog('open');
	});

	$('#btnGrabar').click(function(e){
		//console.log($('#frmNuevo').serialize());
		$.ajax({
		url:'/cliente/grabarSucursal',
		type:'post',
		dataType:'json',
		data:$('#frmNuevo').serialize(),
		success:function(respuesta){
			console.log(respuesta);
			nombresucursal=$('#nombresucursal').val();
			direccion_fiscal=$('#direccion_fiscal').val();
			id=respuesta.idsucursal;
			if (respuesta.validacion==true) {
				nuevaFila(nombresucursal,direccion_fiscal,id);
				$('#contenedor').dialog('close');
			}else{

			}
			
		},
		error:function(error){
			console.log('error');
		}
		});
	});

	$('#btnActualizar').click(function(e){
		//console.log($('#frmNuevo').serialize());
		$.ajax({
		url:'/cliente/actualizarSucursal/'+idclientesucursal,
		type:'post',
		dataType:'json',
		data:$('#frmNuevo').serialize(),
		success:function(respuesta){
			//console.log(respuesta);
			
			if (respuesta.validacion==true) {
				padregeneral.find('.ns').html(respuesta.nombresucursal);
				padregeneral.find('.df').html(respuesta.direccion_fiscal);
				$('#contenedor').dialog('close');
			}else{

			}
			
		},
		error:function(error){
			console.log('error');
		}
		});
	});
	
});

//Muestra el cuadro de dialogo para agregar un nuevo transporte para el cliente
function nuevoTransporte(nameButton){
	$.ajaxSetup({
	cache: false
	});
	$.msgbox("Transporte Nuevos", '<div id="msgboxTransporte"></div>', '<a href="#" id="' + nameButton + '">Agregar</a>');
	$('#msgboxTransporte').load('/forms/transporte.phtml', function(){
		$('#txtTransporte').focus();
		execute();
	});
}

function cargaSucursal(idclientesucursal){
	$.ajax({
		url:'/cliente/cargaSucursal',
		type:'post',
		dataType:'json',
		data:{'idclientesucursal':idclientesucursal},
		success:function(resp){
			console.log(resp);
			$('#nombresucursal').val(resp.nombresucursal);
			$('#nomcontacto').val(resp.nomcontacto);
			$('#dnicontacto').val(resp.dnicontacto);
			$('#telcontac').val(resp.telcontac);
			$('#movilcontac').val(resp.movilcontac);
			$('#direccion_fiscal').val(resp.direccion_fiscal);
			$('#horarioatencion').val(resp.horarioatencion);

			$('#nombrecontactodespacho').val(resp.nombrecontactodespacho);
			$('#dnidespacho').val(resp.dnidespacho);
			$('#telcontacdespacho').val(resp.telcontacdespacho);
			$('#movilcontacdespacho').val(resp.movilcontacdespacho);
			$('#direccion_despacho_contacto').val(resp.direccion_despacho_contacto);
			$('#horarioatenciondespacho').val(resp.horarioatenciondespacho);
			$('#idzona').val(resp.idzona);
			$('#contenedor').dialog('open');
		}
	});
}
function eliminarSucursal(idclientesucursal,padre){
	$.ajax({
		url:'/cliente/eliminarSucursal',
		type:'post',
		dataType:'json',
		data:{'idclientesucursal':idclientesucursal},
		success:function(resp){
			console.log(resp);
			if (resp.validacion) {
				padre.css('display','none');
			}
			
		}
	});
}
function nuevaFila(nombresucursal,direccion_fiscal,idclientesucursal){
	var fila;
	fila="<tr>"+
		 "<td class='ns'>"+nombresucursal+"</td>"+
		 "<td class='df'>"+direccion_fiscal+"</td>"+
		 "<td><a  class='btnEditarSucursal' href='#''><img src='/imagenes/editar.gif'></a> <a  style='margin-left:5px;' class='btnEliminarSucursal' href='#'><img src='/imagenes/eliminar.gif'></a> <input type='hidden' class='idclientesucursal' value="+idclientesucursal+"> </td>"+
		 "</tr>";

	$('#tblSucursal').append(fila);
}
