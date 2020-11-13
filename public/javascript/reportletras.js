$(document).ready(function(){
	listas=$('#listas');
	lstCategoriaPrincipal=$('#lstCategoriaPrincipal');
	lstCategoria=$('#lstCategoria');
	lstZona=$('#lstZona');
	lstTipoCobranza=$('#lstTipoCobranza');
	lstvendedor=$('#lstvendedor');
	fechaInicio=$('#fechaInicio');
	fechaFinal=$('#fechaFinal');
	lstTipoCobro=$('#lstTipoCobro');
        lstRecepcionLetras=$('#lstRecepcionLetras');
        
	lstOctavas=$('#lstOctavas');
	valoropcional=$('#valoropcional');
	IdCliente=$('#txtIdCliente');
	IdOrdenVenta=$('#txtIdOrdenVenta');
	enviar=$('#enviar');
        enviarZona=$('#enviarZona');
        
        var numero;
	var idcobrador;
	tblreportes=$('#tblreportes');
	var parametro;
	var titulo;
        var valor3;
        var title2 = "Letras Protestadas";
        
        $("#lstCategoriaPrincipal").on("change",function(){
        valor=$(this).attr("value");
        if(valor==0){
           $("#lstCategoria").prop('selectedIndex', 0);
         
            
        }
        });
        
        $('#txtCliente').autocomplete({
		source: "/cliente/autocomplete2/",
		select: function(event, ui){
			$('#idCliente').val(ui.item.id);
			
	}});

	$('#txtVendedor').autocomplete({
		source: "/vendedor/autocompletevendedor/",
		select: function(event, ui){
			$('#idVendedor').val(ui.item.id);
		}
	});
        $('#contenedorModal2').css('overflow', 'auto').dialog({
            title: title2,
            autoOpen: false,
            modal: true,
            width: 900,
            height: 350,
            resizable: false,
            draggable: true,
        });
        
        $("#frmLetras").on("submit",function(){
          lstCategoriaPrincipal= $("#lstCategoriaPrincipal option:selected").text();
          lstCategoria= $("#lstCategoria option:selected").text();
          lstZona= $("#lstZona option:selected").text();
          
          
          val_lstCategoriaPrincipal= $("#lstCategoriaPrincipal").attr("value");
          val_lstCategoria= $("#lstCategoria").attr("value");
          val_lstZona= $("#lstZona").attr("value");
          if(val_lstCategoriaPrincipal==""){
             condiciones="Todas las zonas";
          }else{ condiciones=lstCategoriaPrincipal; }
          if(val_lstCategoria==""){
           condiciones += "" ;
          }else{ condiciones+=" - "+lstCategoria; }
          if(val_lstZona==""){
            condiciones += "";
         }else{ condiciones+=" - "+lstZona; }
          
          
          $("#txtCondiciones").attr("value",condiciones);
          
        });
        
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

   fechaInicio.datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy/mm/dd',
      prevText: '<Ant',
      nextText: 'Sig>',
      //showOn: 'button',
      //clearText: 'Borra',
	  //buttonImage: '/imagenes/calendar.png',
	  //buttonImageOnly: true,
      monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
      dayNamesMin: ['Dom', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
    });

    fechaFinal.datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy/mm/dd',
      prevText: '<Ant',
      nextText: 'Sig>',
      monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
      dayNamesMin: ['Dom', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
    });
    $('#fechaPagoInicio').datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy/mm/dd',
      prevText: '<Ant',
      nextText: 'Sig>',
      //showOn: 'button',
      //clearText: 'Borra',
	  //buttonImage: '/imagenes/calendar.png',
	  //buttonImageOnly: true,
      monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
      dayNamesMin: ['Dom', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
    });

    $('#fechaPagoFinal').datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy/mm/dd',
      prevText: '<Ant',
      nextText: 'Sig>',
      monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
      dayNamesMin: ['Dom', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
    });

	$('#lstTipoCobro').change(function(){
		if ($(this).val()==3) {
			$('#octava').removeAttr('disabled');
			$('#novena').removeAttr('disabled');
                        $('#lstRecepcionLetras').val('').removeAttr('disabled');
                        $('#lstDeBanco').val('').attr('disabled','disabled');
		}else if ($(this).val()==4) {
                        $('#lstRecepcionLetras').val("");
			$('#octava').attr('disabled','disabled').removeAttr('checked');
			$('#novena').attr('disabled','disabled').removeAttr('checked');
                        $('#lstDeBanco').val('').attr('disabled','disabled');
                        $('#lstRecepcionLetras').removeAttr('disabled');
		} else {
			$('#octava').attr('disabled','disabled').removeAttr('checked');
			$('#novena').attr('disabled','disabled').removeAttr('checked');
                        $('#lstRecepcionLetras').val('').attr('disabled','disabled');
                        $('#lstDeBanco').val('').attr('disabled','disabled');
		}
	});
        
        $('#lstRecepcionLetras').change(function(){
		if ($(this).val()==1) {
                        $('#lstDeBanco').removeAttr('disabled');
		}else{
                        $('#lstDeBanco').val('').attr('disabled','disabled');
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
        enviarZona.click(function(e){
		e.preventDefault();
		console.log(lstTipoCobro.val());
		console.log(fechaFinal.val());
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
			
		ruta='/reporte/reporteletraszona';
		$('#tblreportes').css('border','none');
		$('#tblreportes').html('<th style="text-align: center;"><img style="width:250px;heigth:100" src="/imagenes/cargando.gif"></th>');
		console.log("haber:"+lstRecepcionLetras.val());
		buscarContenido(ruta,lstZona.val(),lstCategoriaPrincipal.val(),lstCategoria.val(),lstvendedor.val(),lstTipoCobranza.val(),fechaInicio.val(),$('#fechaFinal').val(),lstTipoCobro.val(),titulo,pendiente,cancelado,octava,novena,idcobrador,$('#fechaPagoInicio').val(),$('#fechaPagoFinal').val(),IdCliente.val(),IdOrdenVenta.val());
                $('#exportarExcel').data('tipo', 2);
		encabezadoReporte();
		
	});
	enviar.click(function(e){
		e.preventDefault();
		console.log(lstTipoCobro.val());
		console.log(fechaFinal.val());
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
			
		ruta='/reporte/reporteletras';
		$('#tblreportes').css('border','none');
		$('#tblreportes').html('<th style="text-align: center;"><img style="width:250px;heigth:100" src="/imagenes/cargando.gif"></th>');
		console.log("haber:"+lstRecepcionLetras.val());
		buscarContenido(ruta,lstZona.val(),lstCategoriaPrincipal.val(),lstCategoria.val(),lstvendedor.val(),lstTipoCobranza.val(),fechaInicio.val(),$('#fechaFinal').val(),lstTipoCobro.val(),titulo,pendiente,cancelado,octava,novena,idcobrador,$('#fechaPagoInicio').val(),$('#fechaPagoFinal').val(),IdCliente.val(),IdOrdenVenta.val());
                $('#exportarExcel').data('tipo', 2);
		encabezadoReporte();
		
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
        $('#letrasbanco').click(function(e){
            e.preventDefault();
               $('#total1soles').html('');
            if($('#dietariosoles').val() !=="" && $('#dietariodolares').val() !=="" ){
             
              $.post('/reporte/reportegeneral','idtipocobro='+ $(this).val(),function(data){
                  console.log(data);
                  var totalLProtestadasSoles=parseFloat(data.sumaLetrasxFirmarSinPAS  + data.sumaLetrasxFirmarConPAS).toFixed(2);
                  var totaLProtestadasDolares=parseFloat(data.sumaLetrasxFirmarSinPAD + data.sumaLetrasxFirmarConPAD).toFixed(2);
                  var totaCreditoSoles=parseFloat(data.totalLimaCreditoS + data.totalProvCreditoS).toFixed(2);
                  var totaCreditoDolares=parseFloat(data.totalLimaCreditoD + data.totalProvCreditoD).toFixed(2);
                  var totalS=parseFloat(parseFloat($('#dietariosoles').val()) + data.totalsoles + totalLProtestadasSoles).toFixed(2);
                  var totalD=parseFloat(parseFloat($('#dietariodolares').val())+ data.totaldolares + totaLProtestadasDolares).toFixed(2);
                  var totalprotestosoles=parseFloat(data.totalLimaSoles + data.totalProvinciaSoles + data.totalProvZonaSurSoles).toFixed(2);
                  var totalprotestosDolares=parseFloat(data.totalLimaDolares + data.totalProvinciaDolares + data.totalProvZonaSurDolares).toFixed(2);
                  var totalEmpresaSoles=parseFloat(data.totalParuro_FS + data.totalParuro_PS + data.totalMuestrasS + data.totalUsoExclusivoS + data.totalPrestamoPersonalS).toFixed(2);
                  var totalEmpresaDolares=parseFloat(data.totalParuro_FD + data.totalParuro_PD + data.totalMuestrasD + data.totalUsoExclusivoD + data.totalPrestamoPersonalD).toFixed(2);
                  
                  $('#lprotestadas_soles').attr('value',data.totalsoles);
                  $('#valor2').attr('value',totalLProtestadasSoles);
                  $('#valor5').attr('value',totaLProtestadasDolares);
                  $('#lprotestadas_dolares').attr('value',data.totaldolares);
                  $('#totalsoles').attr('value',parseFloat(parseFloat($('#dietariosoles').val()) + data.totalsoles + totalLProtestadasSoles).toFixed(2));
                  $('#totaldolares').attr('value',parseFloat(parseFloat($('#dietariodolares').val())+ data.totaldolares + totaLProtestadasDolares).toFixed(2));
                  $('#totalLimaSoles').attr('value',data.totalLimaSoles);
                  $('#totalLimaDolares').attr('value',data.totalLimaDolares);
                  $('#totalProvinciaSoles').attr('value',data.totalProvinciaSoles);
                  $('#totalProvinciaDolares').attr('value',data.totalProvinciaDolares);
                 
                  $('#lprotestadasSoles').attr('value',totalprotestosoles);
                  $('#lprotestadasDolares').attr('value',totalprotestosDolares);
                  $('#totalZonaIncobrablesSoles').attr('value',data.totalProvZonaSurSoles);
                  $('#totalZonaIncobrablesDolares').attr('value',data.totalProvZonaSurDolares);
                  
                  $('#totalLetrasConPASoles').attr('value',data.sumaLetrasxFirmarConPAS);
                  $('#totalLetrasConPADolares').attr('value',data.sumaLetrasxFirmarConPAD);
                  $('#letrasSinPASoles').attr('value',data.sumaLetrasxFirmarSinPAS);
                  $('#letrasSinPADolares').attr('value',data.sumaLetrasxFirmarSinPAD);
                  $('#totalLProtestadasSoles').attr('value',data.sumaLetrasxFirmarSinPAS  + data.sumaLetrasxFirmarConPAS);
                  $('#totaLProtestadasDolares').attr('value',data.sumaLetrasxFirmarSinPAD + data.sumaLetrasxFirmarConPAD);
                  $('#creditoLimaSoles').attr('value',data.totalLimaCreditoS);
                  $('#creditoLimaDolares').attr('value',data.totalLimaCreditoD);
                  $('#creditoProvSoles').attr('value',data.totalProvCreditoS);
                  $('#creditoProvDolares').attr('value',data.totalProvCreditoD);
                  $('#totalCreditoSoles').attr('value',totaCreditoSoles);
                  $('#totaLCreditoDolares').attr('value',totaCreditoDolares);
                  $('#totalEmpresaSoles').attr('value',totalEmpresaSoles);
                  $('#totalEmpresaDolares').attr('value',totalEmpresaDolares);
                  
                  $('#valor1').attr('value',$('#dietariosoles').val());
                  $('#valor4').attr('value',$('#dietariodolares').val());
                  
                  $('#tiendaParuroFSoles').attr('value',data.totalParuro_FS);
                  $('#tiendaParuroFDolares').attr('value',data.totalParuro_FD);
                  $('#tiendaParuroPSoles').attr('value',data.totalParuro_PS);
                  $('#tiendaParuroPDolares').attr('value',data.totalParuro_PD);
                  $('#mustrasSoles').attr('value',data.totalMuestrasS);
                  $('#muestrasDolares').attr('value',data.totalMuestrasD);
                  $('#usoExclusivoSoles').attr('value',data.totalUsoExclusivoS);
                  $('#usoExclusivoDolares').attr('value',data.totalUsoExclusivoD);
                  $('#prestamoPersonalSoles').attr('value',data.totalPrestamoPersonalS);
                  $('#prestamoPersonalDolares').attr('value',data.totalPrestamoPersonalD);
                  
                  $('#total1soles').append('<h3>S/. ' + totalS + '</h3>');
                  $('#total1dolares').append('<h3>US $ ' + totalD + '</h3>');
                  $('#totalLetrassoles').append('<h3>S/. ' + totalprotestosoles + '</h3>');
                  $('#totalLetrasdolares').append('<h3>US $ ' + totalprotestosDolares + '</h3>');
                  $('#totaxtrabajarsoles').append('<h3>S/. ' + totalLProtestadasSoles + '</h3>');
                  $('#letrasxtrabajardolares').append('<h3>US $ ' + totaLProtestadasDolares + '</h3>');
                  $('#creditosoles').append('<h3>S/. ' + totaCreditoSoles + '</h3>');
                  $('#creditodolares').append('<h3>US $ ' + totaCreditoDolares + '</h3>');
                  $('#empresaSoles').append('<h3>S/. ' + totalEmpresaSoles + '</h3>');
                  $('#empresaDolares').append('<h3>US $ ' + totalEmpresaDolares + '</h3>');
                  
                  $('#reporteGeneral').show();
                  
              },'json');
               
            } 
            else{
                alert("ingrese el monto de dinero de los dietarios en soles y dolares");
                $('#dietariosoles').focus();
            }
         });
         
         $('#contenedorFormato').dialog({
		autoOpen:false,
		modal:true,
		width:390,
		buttons:{
			"PDF":function(){
			    $('#from-generalcobranza').attr('action', '/pdf/reportegeneralcobranzas');
                            $('#from-generalcobranza').submit();
                            $('#contenedorFormato').dialog('close');
			}, "EXCEL":function(){
                            $('#from-generalcobranza').attr('action', '/excel/reportegeneralcobranzas');
                            $('#from-generalcobranza').submit();
                            $('#contenedorFormato').dialog('close');
			}
		},
		close:function(){
                    $('#contenedorFormato').dialog('close');
		}
	})
         
         $('#expGeneral').click(function(e){
		e.preventDefault();
		console.log(lstTipoCobro.val());
		console.log(fechaFinal.val());
		console.log(fechaInicio.val());
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
                tipocobro=($('#lstTipoCobro option:selected').html());
                ubicacion = ($('#lstCategoriaPrincipal option:selected').html()) + "//" + ($('#lstCategoria option:selected').html()) + "//" + ($('#lstZona option:selected').html());
		ruta='';

		buscarContenidoGC(ruta,ubicacion,lstZona.val(),lstCategoriaPrincipal.val(),lstCategoria.val(),lstvendedor.val(),lstTipoCobranza.val(),$('#fechaInicio').val(),$('#fechaFinal').val(),lstTipoCobro.val(),titulo,pendiente,cancelado,octava,novena,idcobrador,$('#fechaPagoInicio').val(),$('#fechaPagoFinal').val(),IdCliente.val(),IdOrdenVenta.val(),vendedor,tipocobro,lstRecepcionLetras.val());
                $('#contenedorFormato').dialog('open');
	});
        
        $('#tblreportes').on('click', '.verReferencia', function (e) {
            e.preventDefault();
            var iddoc = $(this).data('iddetalleordencobro');
            title2 = "Letra Protestada";
            var iddoc = $(this).data('iddetalleordencobro');
    //        $('#tblcontenedor tr').removeClass();
    //        $(this).parents('tr').addClass('active-row');
            $.ajax({
                url: '/reporte/verdocLetraprotestada',
                type: 'post',
                datatype: 'html',
                data: {'iddetalleordencobro': iddoc},
                success: function (resp) {
                    $('#blockProtestos').html(resp);
                    $('#contenedorModal2').dialog('open');
                }
            });
            return false;
        });
        
        $('#mostrar').click(function(e){
		e.preventDefault();
                $('#tblreportesHead').hide('');
		console.log(lstTipoCobro.val());
		console.log(fechaFinal.val());
		console.log(fechaInicio.val());
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
                tipocobro = '';
                if ($('#lstTipoCobro').val()!="") {
                    tipocobro=($('#lstTipoCobro option:selected').html());
	        }
                
                tipoBanco = '';
                if ($('#lstDeBanco').val()!="") {
                    tipoBanco=($('#lstDeBanco option:selected').html());
	        }
		ruta='/reporte/reporteletrasdetalladas';
		$('#tblreportes').css('border','none');
		$('#tblreportes').html('<th style="text-align: center;"><img style="width:250px;heigth:100" src="/imagenes/cargando.gif"></th>');
		
		buscarContenido(ruta,lstZona.val(),lstCategoriaPrincipal.val(),lstCategoria.val(),lstvendedor.val(),lstTipoCobranza.val(),$('#fechaInicio').val(),$('#fechaFinal').val(),lstTipoCobro.val(),titulo,pendiente,cancelado,octava,novena,idcobrador,$('#fechaPagoInicio').val(),$('#fechaPagoFinal').val(),IdCliente.val(),IdOrdenVenta.val(),vendedor,tipocobro,lstRecepcionLetras.val(),tipoBanco);
		$('#exportarExcel').data('tipo', 1);	
		encabezadoReporte();
//		
	});
        
        $('#txtBusqueda').autocomplete({
		source: "/ordenventa/busquedaletras/",
                minLength: 2,
		select: function(event, ui){
			var numeroLetra=ui.item.label;
                        console.log(numeroLetra);
			Mostrarplanilla(numeroLetra);
                        valor=1;
			actualizaCampo(numeroLetra,valor);
		}
	})
        $('body').on('click','.btnQuitarDetalleMovimientos', function(e){
		e.preventDefault();
		$fila = $(this).parents('tr');
                numeroLetra=$(this).attr('rel');
                console.log(numeroLetra);
		$fila.remove();
                 valor=0;
                actualizaCampo(numeroLetra,valor);
	});
    
    $("#orderDireccion").on('change',function() {
        if( $(this).is(':checked') ) { $(this).val(1); }else { $(this).val(0);  }
    });
});
    function Mostrarplanilla(numeroLetra, idordenventa){
       
       $.post('/reporte/crearplanilla/',{numeroLetra:numeroLetra, idordenventa: idordenventa},function(data){
         $('.body').show();
           console.log(data);
          var contador = parseInt($('#contador').val()) + 1;
          $('#contador').attr('value', contador);
          $('#planilla tbody').append("<tr>" +
                                        "<td>" + data.nombrecli + "<input type='hidden' name='letra[" + contador + "][nombrecli]' value='" + data.nombrecli + "'>" + "</td>" +
                                        "<td>" + data.apellido1 + "<input type='hidden' name='letra[" + contador + "][apellido1]' value='" + data.apellido1 + "'>" + "</td>" +
                                        "<td>" + data.apellido2 + "<input type='hidden' name='letra[" + contador + "][apellido2]' value='" + data.apellido2 + "'>" + "</td>" +
                                        "<td>" + data.doc + "<input type='hidden' name='letra[" + contador + "][doc]' value='" + data.doc + "'>" + "</td>" +
                                        "<td>" + data.tipodoc + "<input type='hidden' name='letra[" + contador + "][tipodoc]' value='" + data.tipodoc + "'>" +"</td>" +
                                        "<td>" + data.numeroletra + "<input type='hidden' name='letra[" + contador + "][nombreletra]' value='" + data.numeroletra + "'>" + "</td>" +
                                        "<td>" + data.fvencimiento + "<input type='hidden' name='letra[" + contador + "][fvencimiento]' value='" + data.fvencimiento + "'>" + "</td>" +
                                        "<td>" + data.simbolo + ' ' + data.importedoc + "<input type='hidden' name='letra[" + contador + "][simbolo]' value='" + data.simbolo + "'>" + "<input type='hidden' name='letra[" + contador + "][importedoc]' value='" + data.importedoc + "'>" + "</td>" +
                                        "<td><a href='#' class='btnQuitarDetalleMovimientos' rel='"+ data.numeroletra + "'><img src='/imagenes/eliminar.gif'></a></td>" +
                                        "</tr>"
                                      ); 
          $('#txtBusqueda').val('');
           
       },'json');
    }
    function actualizaCampo(numeroLetra,valor){
       
       $.post('/reporte/actualizaCampo/',{numeroLetra:numeroLetra,valor:valor},function(data){
             console.log(data);
       },'json');
    }
    
    
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

function buscarContenidoGC(ruta,ubicacion,idzona,idcategoriaprincipal,idcategoria,idvendedor,idtipocobranza,fechaInicio,fechaFinal,idtipocobro,titulo,pendiente,cancelado,octava,novena,idcobrador,fechaPagoInicio,fechaPagoFinal,IdCliente,IdOrdenVenta,vendedor,tipocobro,recepcionLetras){
    $('#idGC-idzona').val(idzona);
    $('#idGC-ubicacion').val(ubicacion);
    $('#idGC-idcategoriaprincipal').val(idcategoriaprincipal);
    $('#idGC-idcategoria').val(idcategoria);
    $('#idGC-idvendedor').val(idvendedor);
    $('#idGC-idtipocobranza').val(idtipocobranza);
    $('#idGC-fechaInicio').val(fechaInicio);
    $('#idGC-fechaFinal').val(fechaFinal);
    $('#idGC-idtipocobro').val(idtipocobro);
    $('#idGC-titulo').val(titulo);
    $('#idGC-pendiente').val(pendiente);
    $('#idGC-cancelado').val(cancelado);
    $('#idGC-octava').val(octava);
    $('#idGC-novena').val(novena);
    $('#idGC-idcobrador').val(idcobrador);
    $('#idGC-fechaPagoFinal').val(fechaPagoFinal);
    $('#idGC-fechaPagoInicio').val(fechaPagoInicio);
    $('#idGC-IdCliente').val(IdCliente);
    $('#idGC-IdOrdenVenta').val(IdOrdenVenta);
    $('#idGC-vendedor').val(vendedor);
    $('#idGC-tipocobro').val(tipocobro);
    $('#idGC-recepcionLetras').val(lstRecepcionLetras.val());
}

function buscarContenido(ruta,idzona,idcategoriaprincipal,idcategoria,idvendedor,idtipocobranza,fechaInicio,fechaFinal,idtipocobro,titulo,pendiente,cancelado,octava,novena,idcobrador,fechaPagoInicio,fechaPagoFinal,IdCliente,IdOrdenVenta,vendedor,tipocobro,recepcionLetras,tipoBanco){
	
        //alert(lstRecepcionLetras.val());
        recepcionLetras = lstRecepcionLetras.val();
        orderDireccion=$('#orderDireccion').attr("value");
        
	$.ajax({
		url:ruta,
		type:'post',
		datatype:'html',
		data:{'idzona':idzona,'idcategoriaprincipal':idcategoriaprincipal,'idcategoria':idcategoria,'idvendedor':idvendedor,'idtipocobranza':idtipocobranza,'fechaInicio':fechaInicio,'fechaFinal':fechaFinal,'idtipocobro':idtipocobro,'titulo':titulo,'pendiente':pendiente,'cancelado':cancelado,'octava':octava,'novena':novena,'idcobrador':idcobrador,'fechaPagoFinal':fechaPagoFinal,'fechaPagoInicio':fechaPagoInicio,'IdCliente':IdCliente,'IdOrdenVenta':IdOrdenVenta,'vendedor':vendedor,'tipocobro':tipocobro,'recepcionLetras':recepcionLetras,'orderDireccion':orderDireccion,'tipoBanc':tipoBanco},
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
function encabezadoReporte(){
	if ($('#lstCategoriaPrincipal').val()!="") {
		$('#txtZonaGeografica').html($('#lstCategoriaPrincipal option:selected').html());
	}else{
		$('#txtZonaGeografica').html('Todo');
	}
	if ($('#lstCategoria').val()!="") {
		$('#txtZonaCobranza').html($('#lstCategoria option:selected').html());
	}else{
		$('#txtZonaCobranza').html('Todo');
	}
	if ($('#lstZona').val()!="") {
		$('#txtZona').html($('#lstZona option:selected').html());
	}else{
		$('#txtZona').html('Todo');
	}
	if ($('#lstTipoCobranza').val()!="") {
		$('#txtTipoCobranza').html($('#lstTipoCobranza option:selected').html());
	}else{
		$('#txtTipoCobranza').html('Todo');
	}
	if ($('#lstvendedor').val()!="") {
		$('#txtVendedor').html($('#lstvendedor option:selected').html());
	}else{
		$('#txtVendedor').html('Todo');
	}
	if ($('#lstcobrador').val()!="") {
		$('#txtCobrador').html($('#lstcobrador option:selected').html());
	}else{
		$('#txtCobrador').html('Todo');
	}
	if ($('#lstTipoCobro').val()!="") {
		$('#txtTipoCobro').html($('#lstTipoCobro option:selected').html());
	}else{
		$('#txtTipoCobro').html('Todo');
	}
	if ($('#fechaInicio').val()!="") {
		$('#txtFechaInicio').html($('#fechaInicio').val());
	}else{
		$('#txtFechaInicio').html('');
	}
	if ($('#fechaFinal').val()!="") {
		$('#txtFechaFinal').html($('#fechaFinal').val());
	}else{
		$('#txtFechaFinal').html('');
	}
	if ($('#octava').attr('checked')=='checked') {
		$('#txtOctavas').html('Octavas');
	}else{
		$('#txtOctavas').html('');
	}
	if ($('#novena').attr('checked')=='checked') {
		$('#txtNovenas').html('Novenas');
	}else{
		$('#txtNovenas').html('');
	}
	if ($('#pendiente').attr('checked')=='checked') {
		$('#txtPendiente').html('Pendiente');
	}else{
		$('#txtPendiente').html('');
	}
	if ($('#cancelado').attr('checked')=='checked') {
		$('#txtCancelado').html('cancelado');
	}else{
		$('#txtCancelado').html('');
	}
	if ($('#txtClientexIdCliente').val()!="") {
		$('#txtCliente').html($('#txtClientexIdCliente').val());
	}else{
		$('#txtCliente').html('');
	}
	if ($('#txtOrdenVentaxId').val()!="") {
		$('#txtOrdenVenta').html($('#txtOrdenVentaxId').val());
	}else{
		$('#txtOrdenVenta').html('');
	}	

	  $("#frmLetrasEvaluacion").on("submit",function(){
          lstCategoriaPrincipal= $("#lstCategoriaPrincipal option:selected").text();
          lstCategoria= $("#lstCategoria option:selected").text();
          lstZona= $("#lstZona option:selected").text();
          
          
          val_lstCategoriaPrincipal= $("#lstCategoriaPrincipal").attr("value");
          val_lstCategoria= $("#lstCategoria").attr("value");
          val_lstZona= $("#lstZona").attr("value");
          if(val_lstCategoriaPrincipal==""){
             condiciones="Todas las zonas";
          }else{ condiciones=lstCategoriaPrincipal; }
          if(val_lstCategoria==""){
           condiciones += "" ;
          }else{ condiciones+=" - "+lstCategoria; }
          if(val_lstZona==""){
            condiciones += "";
         }else{ condiciones+=" - "+lstZona; }
          
          
          $("#txtCondiciones").attr("value",condiciones);
          
        });


}
