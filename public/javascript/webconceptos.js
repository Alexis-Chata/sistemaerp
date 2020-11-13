$(document).ready(function(){
	$(':submit').click(function(){
		cargaOpciones();
		
	});
	
	$(":checkbox").click(function() {
		asignaOpcion($(this).attr('name'),$(this).attr('checked'),$(this).attr('id'));
	});
	$('#txtPostulante').autocomplete({
		source: "/postulante/buscarautocomplete/"
	});	
	$('#btnFavoritos').click(function(e){
		e.preventDefault();
		url=document.URL;
		alert(url);
		agregarFavoritos();
	})
	
	
});

//Carga Opciones del Sistema
function cargaOpciones(){
	idRol = $('#lstRol option:selected').val();
	ruta="/rol/buscarxid/";
	$.post(ruta,{Rol: idRol},function(data){
		$('#DatosRol').html(data);
	});
	
}


function asignaOpcion(idOpcion,checkeado,idRol){
	if(checkeado=="checked"){		
		ruta="/seguridad/asignaropcionxrol/";
			$.post(ruta,{Rol: idRol,Opcion:idOpcion}, function(data){
				//alert("Mensaje:"+data);
				
		});
	}else{
		ruta="/seguridad/desasignaropcionxrol/";
		$.post(ruta,{Rol: idRol,Opcion:idOpcion}, function(data){
				//alert("Mensaje:"+data);
		});
	}
	
}

function agregarFavoritos(){
	$.ajax({
		url:'/index/agregarFavoritos',
		type:'post',
		dataType:'html',
		data:{'Iruta':$('#Iruta').val(),'Inombre':$('#Inombre').val()},
		success:function(data){
			console.log(data);
		}
	})
}