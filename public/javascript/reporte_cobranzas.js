$(document).ready(function(){
    /*INICIO DE REPORTE_COBRANZAS.JS*/
    $('#enviar').click(function(e){
        console.log("cargando...");
            e.preventDefault();
            parametros = $('#report_cobra').serialize();
            $('#tblreportesCobra').html("cargando ...");

            $.ajax({
                    url:'/reporte/reporte_cobranzas_enviar',
                    type:'post',
                    datatype:'html',
                    data:parametros,
                    success:function(resp){
                        console.log(resp);                            
                        $('#tblreportesCobra').html(resp);
                        //alert('entro3');
//                                $('#tblreportes').html('');
//                                $('#tblreportes').html(resp);
//                                $('#tblreportes').css('border','1px solid');
//                                $('#exportarExcel').removeAttr('style');
//                                //alert('Consulta Finalizada');
                    },
                    error:function(error){
                            console.log('error');
                    },
                    complete:function(){
                            console.log('entro final');
                    }

            });

    });
    /* FIN DEL REPORTE_COBRANZAS.JS*/
    
    
    
    
    
    
    
    
    
    
    
    
    
    //alert("melas");
	listas=$('#listas');
	lstCategoriaPrincipal=$('#lstCategoriaPrincipal');
	lstCategoria=$('#lstCategoria');
	lstZona=$('#lstZona');
	lstvendedor=$('#lstvendedor');
        
	lstOctavas=$('#lstOctavas');
	valoropcional=$('#valoropcional');
	IdCliente=$('#txtIdCliente');
	IdOrdenVenta=$('#txtIdOrdenVenta');
	//enviar=$('#enviar');
        var numero;
	var idcobrador;
	tblreportes=$('#tblreportes');
	var parametro;
	var titulo;
        var valor3;
        
        
        $('#exportarExcel').click(function () {
            if ($(this).data('tipo') == 1) {
                if ($('#pendiente').attr('checked')=="checked") {
			pendiente=1;
		} else {
			pendiente='';
		}
		if ($('#cancelado').attr('checked')=="checked") {
			cancelado=1;
		} else {
			cancelado='';
		}
		if ($('#octava').attr('checked')=="checked") {
			octava=1;
		} else {
			octava='';
		}
		if ($('#novena').attr('checked')=="checked") {
			novena=1;
		} else {
			novena='';
		}
                if ($('#lstvendedor').val()!=""){
                    vendedor=($('#lstvendedor option:selected').html());
	        } else {
                    vendedor="";
                }
                
                if ($('#lstTipoCobro').val()!="") {
                    tipocobro=($('#lstTipoCobro option:selected').html());
	        }	
                reemplazarvalores('/excel/reporteletrasdetalladas',lstZona.val(),lstCategoriaPrincipal.val(),lstCategoria.val(),lstvendedor.val(),lstTipoCobranza.val(),$('#fechaInicio').val(),$('#fechaFinal').val(),lstTipoCobro.val(),titulo,pendiente,cancelado,octava,novena,idcobrador,$('#fechaPagoInicio').val(),$('#fechaPagoFinal').val(),IdCliente.val(),IdOrdenVenta.val(),vendedor,tipocobro);
            } else {
                if ($('#pendiente').attr('checked')=="checked") {
			pendiente=1;
		}else{
			pendiente='';
		}
		if ($('#cancelado').attr('checked')=="checked") {
			cancelado=1;
		}else{
			cancelado='';
		}
		if ($('#octava').attr('checked')=="checked") {
			octava=1;
		}else{
			octava='';
		}
		if ($('#novena').attr('checked')=="checked") {
			novena=1;
		}else{
			novena='';
		}
                reemplazarvalores('/excel/reporteletras',lstZona.val(),lstCategoriaPrincipal.val(),lstCategoria.val(),lstvendedor.val(),lstTipoCobranza.val(),fechaInicio.val(),$('#fechaFinal').val(),lstTipoCobro.val(),titulo,pendiente,cancelado,octava,novena,idcobrador,$('#fechaPagoInicio').val(),$('#fechaPagoFinal').val(),IdCliente.val(),IdOrdenVenta.val(), '', '');
            }
        });

	$('#cancelado').click(function(){
		if ($(this).attr('checked')=='checked') {
			$('#fechaPagoInicio').removeAttr('disabled').css('background','skyblue');
			$('#fechaPagoFinal').removeAttr('disabled').css('background','skyblue');
		}else{
			$('#fechaPagoInicio').attr('disabled','disabled').css('background','silver');
			$('#fechaPagoFinal').attr('disabled','disabled').css('background','silver');
		}
	});

	$('#imprimir').click(function(e){
		e.preventDefault();
		$('th').css('color:green;');
		$('.ocultar').hide();
		$('.ocultarImpresion').hide();
		$('.mostrarImpresion').show();		
		$('.tblchildren').show().css('margin','none').css('padding','none');
		$('.filaOculta').hide();
		imprSelec('muestra');
		$('.ocultar').show();
		$('.tblchildren').show();
		$('.ocultarImpresion').show();
		$('.mostrarImpresion').hide();
	});
        $('#imprimirT').click(function(e){
		e.preventDefault();
		$('th').css('color:green;padding:7px 0px;');
               
		imprSelec('reporteGeneral');
		
	});
        $('·excel').click(function(e){
            e.preventDefault();
        });

	lstCategoriaPrincipal.change(function(){
		titulo='Zona Geografica: '+$('#lstCategoriaPrincipal option:selected').text();
		if ($(this).val()) {
			listaZonaCobranza($(this).val());
		}
	});
	lstCategoria.change(function(){
		titulo='Zona Cobranza: '+$('#lstCategoria option:selected').text();
		if ($(this).val()) {
			listaZonasxCategoria($(this).val());
		}
	});
	lstZona.change(function(){
		titulo='Zona: '+$('#lstZona option:selected').text();
	});
	lstvendedor.change(function(){
		titulo='Vendedor: '+$('#lstvendedor option:selected').text();
	});
	$('#lstcobrador').change(function(){
		idcobrador=$(this).val();
		if (idcobrador!="") {
			
			$('#lstCategoriaPrincipal').val('').attr('disabled','disabled');
			$('#lstCategoria').val('').attr('disabled','disabled');
			$('#lstZona').val('').attr('disabled','disabled');
		}else{

			$('#lstCategoriaPrincipal').removeAttr('disabled');
			$('#lstCategoria').removeAttr('disabled');
			$('#lstZona').removeAttr('disabled');
		}
	});

//   fechaInicio.datepicker({
//      changeMonth: true,
//      changeYear: true,
//      dateFormat: 'yy/mm/dd',
//      prevText: '<Ant',
//      nextText: 'Sig>',
//      //showOn: 'button',
//      //clearText: 'Borra',
//	  //buttonImage: '/imagenes/calendar.png',
//	  //buttonImageOnly: true,
//      monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
//      dayNamesMin: ['Dom', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
//      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
//    });
//
//    fechaFinal.datepicker({
//      changeMonth: true,
//      changeYear: true,
//      dateFormat: 'yy/mm/dd',
//      prevText: '<Ant',
//      nextText: 'Sig>',
//      monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
//      dayNamesMin: ['Dom', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
//      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
//    });
//    $('#fechaPagoInicio').datepicker({
//      changeMonth: true,
//      changeYear: true,
//      dateFormat: 'yy/mm/dd',
//      prevText: '<Ant',
//      nextText: 'Sig>',
//      //showOn: 'button',
//      //clearText: 'Borra',
//	  //buttonImage: '/imagenes/calendar.png',
//	  //buttonImageOnly: true,
//      monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
//      dayNamesMin: ['Dom', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
//      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
//    });
//
//    $('#fechaPagoFinal').datepicker({
//      changeMonth: true,
//      changeYear: true,
//      dateFormat: 'yy/mm/dd',
//      prevText: '<Ant',
//      nextText: 'Sig>',
//      monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
//      dayNamesMin: ['Dom', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
//      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
//    });

	$('#lstTipoCobro').change(function(){
		if ($(this).val()==3) {
			$('#octava').removeAttr('disabled');
			$('#novena').removeAttr('disabled');
                        $('#lstRecepcionLetras').removeAttr('disabled');
		}else{
			$('#octava').attr('disabled','disabled').removeAttr('checked');
			$('#novena').attr('disabled','disabled').removeAttr('checked');
                        $('#lstRecepcionLetras').val('').attr('disabled','disabled');
		}
	});

	$('#octava,#novena').click(function(){
		if ($('#octava').attr('checked')=="checked"  || $('#novena').attr('checked')=="checked") {
			$('#pendiente').removeAttr('checked').attr('disabled','disabled');
			$('#cancelado').removeAttr('checked').attr('disabled','disabled');
			$('#fechaFinal').val('').attr('disabled','disabled');
			$('#fechaInicio').val('').attr('disabled','disabled');
			
		}else{
			$('#pendiente').removeAttr('disabled');
			$('#cancelado').removeAttr('disabled');
			$('#fechaFinal').removeAttr('disabled');
			$('#fechaInicio').removeAttr('disabled');
		}
		
	});
	$('.ocultar').live('click',function(e){
		e.preventDefault();
		padre=$(this).parents('.filaContenedor');
		padre.find('.tblchildren').hide('Blind');
		padre.find('.filaOculta').show('Blind');
	});
	$('.ver').live('click',function(e){
		e.preventDefault();
		
		padre=$(this).parents('.filaContenedor');
		padre.find('.tblchildren').show('Blind');
		padre.find('.filaOculta').hide('Blind');
	});	
	
	lstOctavas.change(function(){
		dias=$(this).val();
		fechaFinal.val(hoy());
		fechaInicio.val(antesdehoy(dias));

		
	});
	function esconder(){
		lstCategoriaPrincipal.hide();
		lstCategoria.hide();
		lstZona.hide();
		lstTipoCobranza.hide();
		lstvendedor.hide();
		fechaInicio.hide();
		fechaFinal.hide();
		enviar.hide();
		lstTipoCobro.hide();
		lstOctavas.hide();
	}
	function mostrar(){
		listas.show();
		fechaInicio.show();
		fechaFinal.show();
		enviar.show();
		lstTipoCobranza.show();
		lstTipoCobro.show();
		lstOctavas.show();
	}
	function limpiar(){
		lstCategoriaPrincipal.val('');
		lstCategoria.val('');
		lstZona.val('');
		lstTipoCobranza.val('');
		lstvendedor.val('');
		fechaInicio.val('');
		fechaFinal.val('');
		lstTipoCobro.val('');
		lstOctavas.val('');
	}
	function verificarNulos(){
		if (parametro.val()=="" || fechaInicio.val()=="" || fechaFinal.val()=="" || lstTipoCobro.val()=="") {
			return false;
		}
		else{
			return true;
		}
	}
	$('#btnLimpiar').click(function(){
		$('#txtZonaGeografica').html('');
		$('#txtZonaCobranza').html('');
		$('#txtZona').html('');
		$('#txtTipoCobranza').html('');
		$('#txtVendedor').html('');
		$('#txtTipoCobro').html('');
		$('#txtFechaInicio').html('');
		$('#txtFechaFinal').html('');
		$('#txtOctavas').html('');
		$('#txtNovenas').html('');
		$('#txtPendiente').html('');
		$('#txtCancelado').html('');
		$('#tblreportes').html('');
		idcobrador='';
		$('#lstcobrador').val('').change();
		$('#txtIdCliente').val('');
		$('#txtIdOrdenVenta').val('');
	});
        
         
        
        
        
        
});
    
    
    
    
function reemplazarvalores (ruta,idzona,idcategoriaprincipal,idcategoria,idvendedor,idtipocobranza,fechaInicio,fechaFinal,idtipocobro,titulo,pendiente,cancelado,octava,novena,idcobrador,fechaPagoInicio,fechaPagoFinal,IdCliente,IdOrdenVenta,vendedor,tipocobro){
    $('#from-mostrar').attr('action', ruta);
    $('#idFM-idzona').val(idzona);
    $('#idFM-idcategoriaprincipal').val(idcategoriaprincipal);
    $('#idFM-idcategoria').val(idcategoria);
    $('#idFM-idvendedor').val(idvendedor);
    $('#idFM-idtipocobranza').val(idtipocobranza);
    $('#idFM-fechaInicio').val(fechaInicio);
    $('#idFM-fechaFinal').val(fechaFinal);
    $('#idFM-idtipocobro').val(idtipocobro);
    $('#idFM-titulo').val(titulo);
    $('#idFM-pendiente').val(pendiente);
    $('#idFM-cancelado').val(cancelado);
    $('#idFM-octava').val(octava);
    $('#idFM-novena').val(novena);
    $('#idFM-idcobrador').val(idcobrador);
    $('#idFM-fechaPagoInicio').val(fechaPagoInicio);
    $('#idFM-fechaPagoFinal').val(fechaPagoFinal);
    $('#idFM-IdCliente').val(IdCliente);
    $('#idFM-IdOrdenVenta').val(IdOrdenVenta);
    $('#idFM-vendedor').val(vendedor);
    $('#idFM-tipocobro').val(tipocobro);
    $('#from-mostrar').submit();
}  

function buscarContenido(ruta,idzona,idcategoriaprincipal,idcategoria,idvendedor,idtipocobranza,fechaInicio,fechaFinal,idtipocobro,titulo,pendiente,cancelado,octava,novena,idcobrador,fechaPagoInicio,fechaPagoFinal,IdCliente,IdOrdenVenta,vendedor,tipocobro,recepcionLetras){
//ruta,idzona,idcategoriaprincipal,idcategoria,idvendedor,idtipocobranza,fechaInicio,fechaFinal,idtipocobro,titulo,pendiente,cancelado,octava,novena,idcobrador,fechaPagoInicio,fechaPagoFinal,IdCliente,IdOrdenVenta,vendedor,tipocobro,recepcionLetras
	
        //alert(lstRecepcionLetras.val());
        recepcionLetras = lstRecepcionLetras.val();
	$.ajax({
		url:ruta,
		type:'post',
		datatype:'html',
		data:{'idzona':idzona,'idcategoriaprincipal':idcategoriaprincipal,'idcategoria':idcategoria,'idvendedor':idvendedor,'idtipocobranza':idtipocobranza,'fechaInicio':fechaInicio,'fechaFinal':fechaFinal,'idtipocobro':idtipocobro,'titulo':titulo,'pendiente':pendiente,'cancelado':cancelado,'octava':octava,'novena':novena,'idcobrador':idcobrador,'fechaPagoFinal':fechaPagoFinal,'fechaPagoInicio':fechaPagoInicio,'IdCliente':IdCliente,'IdOrdenVenta':IdOrdenVenta,'vendedor':vendedor,'tipocobro':tipocobro,'recepcionLetras':recepcionLetras},
		success:function(resp){
			//console.log(resp);
			//alert('entro3');
			$('#tblreportes').html('');
			$('#tblreportes').html(resp);
			$('#tblreportes').css('border','1px solid');
                        $('#exportarExcel').removeAttr('style');
			//alert('Consulta Finalizada');
		},
		error:function(error){
			//console.log('error');
		},
		complete:function(){
			//console.log('entro final');
		}

	});
}


function hoy()
{
    var fechaActual = new Date();
 
    dia = fechaActual.getDate();
    mes = fechaActual.getMonth() +1;
    anno = fechaActual.getFullYear();
   
 
    if (dia <10) dia = "0" + dia;
    if (mes <10) mes = "0" + mes;  
 
    fechaHoy =  anno+ "/" + mes + "/" + dia;
   
    return fechaHoy;
}
function antesdehoy(dias){
	var fechaActual = new Date();
	nuevafecha=new Date(fechaActual.getTime() - (dias * 24 * 3600 * 1000));

	dia = nuevafecha.getDate();
    mes = nuevafecha.getMonth() +1;
    anno = nuevafecha.getFullYear();
   
 
    if (dia <10) dia = "0" + dia;
    if (mes <10) mes = "0" + mes;  
 
    fechaHoy = anno + "/" + mes + "/" +dia ;
    return fechaHoy;

}
function listaZonaCobranza(idpadrec){
	
	$.ajax({
		url:'/zona/listaCategoriaxPadre',
		type:'post',
		datatype:'html',
		data:{'idpadrec':idpadrec},
		success:function(resp){
			console.log(resp);
			$('#lstCategoria').html(resp);
			
		},
		error:function(error){
			console.log('error');
		},
		complete:function(){

		}

	});
}
function listaZonasxCategoria(idzona){
	
	$.ajax({
		url:'/zona/listaZonasxCategoria',
		type:'post',
		datatype:'html',
		data:{'idzona':idzona},
		success:function(resp){
			console.log(resp);
			$('#lstZona').html(resp);
			
		},
		error:function(error){
			console.log('error');
		},
		complete:function(){

		}

	});
}
