$(function() {
    	// Botón para subir la firma
        $('#loaderAjax').hide();
		var btn_firma = $('#addImage'), interval;
			new AjaxUpload('#addImage', {
				action: '/actor/uploadFileImg/',
				onSubmit : function(file , ext){
                                   
					if (! (ext && /^(jpg|png)$/.test(ext))){
						// extensiones permitidas
						alert('Sólo se permiten Imagenes .jpg o .png');
						// cancela upload
						return false;
					} else {
						$('#loaderAjax').show();
						btn_firma.text('Espere por favor');
						this.disable();
					}
				},
				onComplete: function(file, response){
					 //alert(response);
					btn_firma.text('Cambiar Imagen');
					respuesta = $.parseJSON(response);
                                            if(respuesta.respuesta == 'done'){
                                               $('#nombreimagen').attr('value',respuesta.fileName);
						$('#fotografia').removeAttr('scr');
						$('#fotografia').attr('src','/imagenes/actorfoto/' + respuesta.fileName);
						$('#loaderAjax').show();
						// alert(respuesta.mensaje);
					}
					else{
						alert(respuesta.mensaje);
					}
						
					$('#loaderAjax').hide();	
					this.enable();	
				}
		});
    });