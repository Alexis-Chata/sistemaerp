$(document).on('ready', function(){
	/*$var msgboxTitleAlmacen = 'Mantenimiento de Almacen';
	('#frmAlmacenNuevo, #frmAlmacenActualizar').validate({
		invalidHandler: function(form, validator) {
		   var errors = validator.numberOfInvalids();
		   if (errors) {
		        $.msgbox(msgboxTitle, 'Ingrese todos los datos requeridos correctamente');
		     }
		},
		errorElement:'span'
	});*/
	$('#btnCancelar').on('click', function(e){
		e.preventDefault();
		window.location = '/almacen/listar/';
	});
//Boton de eliminacion
	$('.btnEliminarAlmacen').on('click', function(e){
		e.preventDefault();
		var url = $(this).attr('href');
		$.msgbox(msgboxTitleAlmacen, '¿Esta seguro de elimiar el registro?');
		$('#msgbox-ok').click(function(){
			window.location = url;
		});
	});

	$("#seleccion").change(function(){
		var id=$("#seleccion option:selected").text();
		var url='/almacen/listar/'+id;
		window.location=url;
	});

	/*$('#frmAlmacenNuevo').validate({
           rules: {
           'Almacen[nomalm]': { required: true},
           'Almacen[razsocalm]': { required: true},
           'Almacen[diralm]': { required: true},
           'Almacen[rucalm]': { required: true, number: true },
           },
       messages: {
           'Almacen[nomalm]': {required:'Debe ingresar el nombre'} ,
           'Almacen[razsocalm]': {required:'Debe ingresar el apellido'},
           'Almacen[diralm]':{required:'Debe ingresar su direccion'},
           'Almacen[rucalm]':{required: 'Debe ingresar su RUC'}

       }
    });*/
});