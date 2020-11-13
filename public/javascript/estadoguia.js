$(document).ready(function(){
	padregeneral='';
	valoridordenventa=0;
	motivogeneral="";
	$('#imprimir').hide();
	$('#txtOrdenVenta').autocomplete({
		source: "/ordenventa/ListaEstadoGuia/",
		select: function(event, ui){
			var idOrdenVenta=ui.item.id;
			valoridordenventa=ui.item.id;
			MostrarOrdenVenta(idOrdenVenta);
			$('#imprimir').show();
		}
	});
	$('#imprimir').click(function(e){
		$('.grabar').hide();
		$('.editar').hide();
		$('#grabartipocobranza').hide();
		$('#lsttipocobranza').css({'border':'none'});
		$('.recepcionLetra, .numerounico').css({'border':'none'});
		$('.muestra').css({'background-color':'white'});
		imprSelec('muestra')
		$('.grabar').show();
		$('.editar').show();
		$('#grabartipocobranza').show();
		$('#lsttipocobranza').css({'border':'1px solid'});
		$('.recepcionLetra,.numerounico').css({'border':'1px solid'});
	});

	$('.motivo').live('click',function(e){
		motivogeneral=$(this).attr('value');
		//alert(motivogeneral);
		$('#respMotivo').html(motivogeneral);
		$('#contenedorMotivo').dialog('open');
	});

	$('#contenedorMotivo').dialog({
		modal:true,
		width:500,
		autoOpen:false,
		buttons:{
			Aceptar:function(){
				$('#contenedorMotivo').dialog('close');
			}
		},
		close:function(){
			$('#respMotivo').html('');
		}
	});
	
});	

$('.grabar').live('click',function(e){
	e.preventDefault();
	elemento=$(this);
	padregeneral=elemento.parents('tr');
	iddetalleordencobro=padregeneral.find('.iddetalleordencobro').val();
	actualizadetalleordencobro(iddetalleordencobro);
});




$('.editar').live('click',function(e){
	e.preventDefault();
	elemento=$(this);
	padre=elemento.parents('tr');
	recepcionLetra=padre.find('.recepcionLetra');
	recepcionLetra.removeAttr('readonly');
        padre.find('.numerounico').show();
        padre.find('.Textnumerounico').hide();
	//padre.find('.numerounico').removeAttr('readonly').css({'background-color':'red','color':'white'}).focus();
	
	recepcionLetra.css({'background-color':'red'});
	recepcionLetra.css({'color':'white'});
	
});
$('#grabartipocobranza').live('click',function(e){
	//alert('entro');
	e.preventDefault();
	actualizatipocobranza();

});

$('#editarPorcentaje').live('click',function(e){
	e.preventDefault();

	$('#txtPorcentaje').removeAttr('readonly');
	$('#txtPorcentaje').focus();
});
$('#grabarPorcentaje').live('click',function(e){
	e.preventDefault();

	porcentajeComision(valoridordenventa,$('#txtPorcentaje').val());
});

//**


function MostrarOrdenVenta(idordenventa){
	var ruta = "/ordenventa/CabeceraEstadoGuia/" + idordenventa;
	$.post(ruta, function(data){
		$('#tblDetalleOrdenVenta thead').html(data);	
	});
	cargaDetalleOrdenCobro(idordenventa);
	cargaDetalleOrdenVenta(idordenventa);
	cargartabladocumentos(idordenventa);
	
}


function cargaDetalleOrdenVenta(idordenventa){
	var ruta = "/ordenventa/DetalleGuiaMadre/" + idordenventa;
	$.post(ruta, function(data){
		$('#tblDetalleOrdenVenta tbody').html(data);	
	});


}

function cargaDetalleOrdenCobro(idordenventa){
	var ruta = "/ordencobro/buscarDetalleOrdenCobroEstadoGuia/" + idordenventa;
	$.post(ruta, function(data){
		$('#tblDetalleOrdenCobro tbody').html(data);	
	});
}


function cargartabladocumentos(idordenventa){

	var ruta = "/documento/documentosxordenventa/" + idordenventa;
	$.post(ruta, function(data){
		$('#tbldocumentos tbody').html(data);	

	});
}



function actualizadetalleordencobro(iddetalleordencobro) {
	recepcionLetras=padregeneral.find('.recepcionLetra').val();
	numerounico=padregeneral.find('.numerounico').val();
	$.ajax({
		url:'/ordencobro/actualizadetalleordencobro/',
		type:'post',
		datatype:'html',
		data:{'iddetalleordencobro':iddetalleordencobro,'recepcionLetras':recepcionLetras,'numerounico':numerounico,'evaluacion':2},
		success:function(resp){
                    padregeneral.find('.Textnumerounico').html(numerounico);
                    padregeneral.find('.Textnumerounico').show();
                    padregeneral.find('.numerounico').hide();
			recepcionLetra=padregeneral.find('.recepcionLetra');
			recepcionLetra.attr('readonly','readonly');
			recepcionLetra.css({'background-color':'white'});
			recepcionLetra.css({'color':'black'});
			padregeneral.find('.numerounico').css({'background-color':'white','color':'black'}).attr('readonly','readonly')
			console.log(resp);
			
		},
		error:function(error){
			console.log('error');
		}
	});
}


function actualizatipocobranza() {
	idtipocobranza=$('#lsttipocobranza').val();

	$.ajax({
		url:'/ordenventa/actualizatipocobranza/',
		type:'post',
		datatype:'html',
		data:{'idordenventa':valoridordenventa,'idtipocobranza':idtipocobranza},
		success:function(resp){
			
			console.log(resp);
			alert('Se Grabó Correctamente!');
			
		},
		error:function(error){
			console.log('error');
		}
	});
}
function porcentajeComision(idordenventa,porcentaje){

	$.ajax({
		url:'/ordenventa/porcentajeComision',
		type:'post',
		dataType:'html',
		data:{'idordenventa':idordenventa,'porcentaje':porcentaje},
		success:function(resp){
			console.log(resp);
			alert('Se Grabó Correctamente');
		}
	});
}