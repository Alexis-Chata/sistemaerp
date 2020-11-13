$(document).ready(function(){
	
	$(":checkbox").click(function() {
		asignaRol($(this).attr('name'),$(this).attr('checked'),$(this).attr('id'));
	});
	
});


function asignaRol(idRol,checkeado,idActor){
	if(checkeado=="checked"){		
		ruta="/seguridad/asignarrolxactor/";
			$.post(ruta,{Rol: idRol,Actor:idActor}, function(data){
				alert("Mensaje:"+data);
		});
	}else{
		ruta="/seguridad/desasignarrolxactor/";
		$.post(ruta,{Rol: idRol,Actor:idActor}, function(data){
				alert("Mensaje:"+data);
		});
	}
	
}