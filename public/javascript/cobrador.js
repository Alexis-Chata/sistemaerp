$(document).ready(function(){
	$('.categoria').click(function(e){
		
		idcategoria=$(this).attr('id');
		idactor=$('#idactor').val();

		if ($(this).attr("checked")=="checked") {
			console.log('grbar');
			estado=1;
			asignarZonas(idcategoria,idactor,estado);
		}else{
			console.log('entro');
			estado=0;
			asignarZonas(idcategoria,idactor,estado);
		}
		
	});
});

function asignarZonas(idcategoria,idactor,estado){
	$.ajax({
		url:'/cobrador/asignarZonas',
		type:'post',
		datatype:'html',
		data:{'idcategoria':idcategoria,'idactor':idactor,'estado':estado},
		success:function(resp){
			console.log(resp);
		}
	});
}