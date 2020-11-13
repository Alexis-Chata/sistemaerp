$(document).ready(function(){
	//Desactivar Conceptos
	//modoDesactivado();
	//desactivaAdicionales();
	//Costos Fijos
	// $('.txtFleteDetalle,.txtSeguroDetalle,.txtAdvaloremPDetalle,.txtTasaDespachoDetalle,.txtFleteInternoDetalle,.txtCF1Detalle,.txtCF2Detalle').on('blur',function(e){
	// 	e.preventDefault();
	// 	var item=$(this).parents('tr');
	// 	//recalcularDetalles();
	// });

	// $('.txtFlatDetalle,.txtVBDetalle,.txtGateInDetalle,.txtBoxFeeDetalle,.txtInsuranceFeeDetalle,.txtSobreestadiaDetalle,.txtDocFeeDetalle,.txtGasAdmDetalle,.txtAgenteAduanaDetalle,.txtCV1Detalle,.txtCV2Detalle,.txtCV3Detalle').on('blur',function(e){
	// 	e.preventDefault();
	// 	var item=$(this).parents('tr');
	// 	//recalcularDetalles();
	// });
	if ($('#vbimportaciones').val()==1) {
		$('input').attr('disabled','disabled');
		$('select').attr('disabled','disabled');
		
		$('#btnRegistrarOrden').css('display','none');
	}

	$('#btnRegistrarOrden').click(function(e){
		if ($('#conformidad').attr('checked')=="checked") {
			
			if (!confirm('¿Esta seguro de confirmar una vez grabado no va poder modificar la valorización?')) {
				e.preventDefault();
			}
			
		}
	});

	$('.txtAdvaloremPDetalle').blur(function(){
		item=$(this).parents('tr');
		advaloremp=$(this).val();
		cif=item.find('.txtciftotal').val();
		valorAdvaloren=(advaloremp*cif)/100;
		item.find('.txtAdvaloremVDetalle').val(valorAdvaloren.toFixed(2));
		recalcularxFila(item);
	});
	$('.txtAdvaloremVDetalle').blur(function(){
		var item=$(this).parents('tr');
		recalcularxFila(item);

	});
	$('#btnCancelar').click(function(e){
		e.preventDefault();
		window.location="/importaciones/ordencompra";
	});
        
        $('#btnExcel').click(function(e){
		e.preventDefault();
                id=$('input:hidden[name=idOrdenCompra]').val();
		window.location="/importaciones/exportarordencompra/"+id;
	});        

	$('#cadic1').click(function(){
		console.log($(this).attr('checked'));
		if ($(this).attr('checked')=='checked') {
			activarConcepto('#txtTotalcostocv1OC');
			$('#txtTotalcostocv1OC').focus();
		}else{
			for (var i =1 ; i <= 2; i++) {
				$('.txtCV1Detalle ').val(0);
				desactivarConcepto('#txtTotalcostocv1OC');
				
				recalcularDetalles();
			};
			
		}
	});
	$('#cadic2').click(function(){
		console.log($(this).attr('checked'));
		if ($(this).attr('checked')=='checked') {
			activarConcepto('#txtTotalcostocv2OC');
			$('#txtTotalcostocv2OC').focus();
		}else{
			for (var i =1 ; i <= 2; i++) {
				$('.txtCV2Detalle ').val(0);
				desactivarConcepto('#txtTotalcostocv2OC');
				recalcularDetalles();
			};
		}
	});
	$('#cadic3').click(function(){
		console.log($(this).attr('checked'));
		if ($(this).attr('checked')=='checked') {
			activarConcepto('#txtTotalcostocv3OC');
			$('#txtTotalcostocv3OC').focus();
		}else{
			for (var i =1 ; i <= 2; i++) {
				$('.txtCV3Detalle ').val(0);
				desactivarConcepto('#txtTotalcostocv3OC');
				recalcularDetalles();
			};
		}
	});

	$('#cf1').on('change',function(e){
		e.preventDefault();
		if($(this).is(':checked')){
			activarConcepto('.txtCF1Detalle');
		}else{
			desactivarConcepto('.txtCF1Detalle');

		}
	});
	$('#cf2').on('change',function(e){
		e.preventDefault();
		if($(this).is(':checked')){
			activarConcepto('.txtCF2Detalle');
		}else{
			desactivarConcepto('.txtCF2Detalle');
		}
	});
	$('#cv1').on('change',function(e){
		e.preventDefault();
		if($(this).is(':checked')){
			activarConcepto('.txtCV1Detalle');
		}else{
			desactivarConcepto('.txtCV1Detalle');
		}
	});
	$('#cv2').on('change',function(e){
		e.preventDefault();
		if($(this).is(':checked')){
			activarConcepto('.txtCV2Detalle');
		}else{
			desactivarConcepto('.txtCV2Detalle');
		}
	});		
	$('#PrintTodo').on('click',function(e){
		e.preventDefault();
		imprSelec('PrintdetalleOrdenCompra');
	});
	$('#txtFleteOC, ').on('blur',function(){
		
		calcularParteConceptos(('.txtVolumenDetalle'),('.txtFleteDetalle'),$(this).val());
		recalcularDetalles();
	});	
	$('#txtSeguroOC, #txtTotalcostotasadespOC, #txtTotalcostoflatOC, #txtTotalcostoalmacenvbOC, #txtTotalcostoalmacengateOC, #txtTotalcostocv1OC, #txtTotalcostocv2OC, #txtTotalcostocv3OC, #txtTotalcostocv30C, #txtTotalcomisionagenteaduOC, #txtTotalcostofleteinternoOC').on('blur',function(){
		var id=$(this).attr('id');
		console.log(id);
		montoConcepto=totalConcepto('.txtfobTotalDetalle');
		//console.log(montoConcepto);
		if (id=='txtSeguroOC') {
			calcularParteConceptos(('.txtfobTotalDetalle'),('.txtSeguroDetalle'),$(this).val());
		}else if(id=='txtTotalcostotasadespOC'){
			calcularParteConceptos(('.txtfobTotalDetalle'),('.txtTasaDespachoDetalle'),$(this).val());
		}else if(id=='txtTotalcostoflatOC'){
			calcularParteConceptos(('.txtfobTotalDetalle'),('.txtFlatDetalle'),$(this).val());
		}else if(id=='txtTotalcostoalmacenvbOC'){
			calcularParteConceptos(('.txtfobTotalDetalle'),('.txtVBDetalle'),$(this).val());
		}else if(id=='txtTotalcostoalmacengateOC'){
			calcularParteConceptos(('.txtfobTotalDetalle'),('.txtGateInDetalle'),$(this).val());
		}else if(id=='txtTotalcostocv1OC'){
			calcularParteConceptos(('.txtfobTotalDetalle'),('.txtCV1Detalle'),$(this).val());
		}else if(id=='txtTotalcostocv2OC'){
			calcularParteConceptos(('.txtfobTotalDetalle'),('.txtCV2Detalle'),$(this).val());
		}else if(id=='txtTotalcostocv3OC'){
			calcularParteConceptos(('.txtfobTotalDetalle'),('.txtCV3Detalle'),$(this).val());
		}else if(id=='txtTotalcomisionagenteaduOC'){
			calcularParteConceptos(('.txtfobTotalDetalle'),('.txtAgenteAduanaDetalle'),$(this).val());
		}else if(id=='txtTotalcostofleteinternoOC'){
			calcularParteConceptos(('.txtVolumenDetalle'),('.txtFleteInternoDetalle'),$(this).val());
		}
		
		//console.log($(this).val());
		 recalcularDetalles();
	});

	$('.txtCantidadDetalle, .txtVolumen, .txtfobDetalle').blur(function(){
		
			clase=$(this).attr('class');
			
			if (clase=='txtVolumen numeric required') {
				for (var i =1 ; i <= 2; i++) {
					valor=$('#txtFleteOC').val();
					calcularParteConceptos(('.txtVolumenDetalle'),('.txtFleteDetalle'),valor);
					recalcularDetalles();
				}
			}else {
				for (var i =1 ; i <= 2; i++) {
					calcularParteConceptos(('.txtVolumenDetalle'),('.txtFleteDetalle'),$('#txtFleteOC').val());
					calcularParteConceptos(('.txtfobTotalDetalle'),('.txtSeguroDetalle'),$('#txtSeguroOC').val());
					
					calcularParteConceptos(('.txtfobTotalDetalle'),('.txtTasaDespachoDetalle'),$('#txtTotalcostotasadespOC').val());
					calcularParteConceptos(('.txtfobTotalDetalle'),('.txtFlatDetalle'),$('#txtTotalcostoflatOC').val());
					calcularParteConceptos(('.txtfobTotalDetalle'),('.txtVBDetalle'),$('#txtTotalcostoalmacenvbOC').val());
					calcularParteConceptos(('.txtfobTotalDetalle'),('.txtGateInDetalle'),$('#txtTotalcostoalmacengateOC').val());
					calcularParteConceptos(('.txtfobTotalDetalle'),('.txtCV1Detalle'),$('#txtTotalcostocv1OC').val());
					calcularParteConceptos(('.txtfobTotalDetalle'),('.txtCV2Detalle'),$('#txtTotalcostocv2OC').val());
					calcularParteConceptos(('.txtfobTotalDetalle'),('.txtCV3Detalle'),$('#txtTotalcostocv3OC').val());
					calcularParteConceptos(('.txtfobTotalDetalle'),('.txtAgenteAduanaDetalle'),$('#txtTotalcomisionagenteaduOC').val());
					calcularParteConceptos(('.txtfobTotalDetalle'),('.txtFleteInternoDetalle'),$('#txtTotalcostofleteinternoOC').val());
					recalcularDetalles();
				}
			}
		
	});
	
});
function recalcularDetalles(){

	$('.txtfobDetalle').each(function(){
		element=$(this).parents('tr');
		recalcularxFila(element);
	});
}

function recalcular(element){

	//Costos Fijos
	var volumenUnitario=parseFloat(element.find('.txtVolumen').val());
	var piezas=parseFloat(element.find('.piezas').val());
	

	var fobunitario=parseFloat(element.find('.txtfobDetalle').val());
	var cantidad=parseFloat(element.find('.txtCantidadDetalle').val());
	var carton=cantidad/piezas;
	var nuevoSubTotalFob=fobunitario*cantidad;
	element.find('.txtfobTotalDetalle ').val(nuevoSubTotalFob.toFixed(2));
	element.find('.carton').val(carton);
	var nuevoCBM=volumenUnitario*carton;
	//element.find('.txtVolumenDetalle').val(nuevoCBM.toFixed(2));

	var flete=parseFloat(element.find('.txtFleteDetalle').val());
		$('#txtFleteOC').val(totalConcepto('.txtFleteDetalle').toFixed(2));
	var seguro=parseFloat(element.find('.txtSeguroDetalle').val());
		$('#txtSeguroOC').val(totalConcepto('.txtSeguroDetalle').toFixed(2));
	var cif=(fobunitario*cantidad+flete+seguro);

	var cifunitario=(cif/cantidad);
		element.find('.txtciftotal').val(cif.toFixed(2));
		element.find('.txtcifunitario').val(cifunitario.toFixed(2));
	var cifTotalOC=totalConcepto('.txtciftotal');
		$('#txtTotalCifOC').val(cifTotalOC.toFixed(2));

	var advaloremp=parseFloat(element.find('.txtAdvaloremPDetalle').val());
	var advaloremv=(cif*advaloremp)/100;
		//$('#txtTotaladvaloremOC').val(totalConcepto('.txtAdvaloremVDetalle').toFixed(2));
	var tasadespacho=parseFloat(element.find('.txtTasaDespachoDetalle').val());
		//$('#txtTotalcostotasadespOC').val(totalConcepto('.txtTasaDespachoDetalle').toFixed(2));
	var fleteinterno=parseFloat(element.find('.txtFleteInternoDetalle').val());
		//$('#txtTotalcostofleteinternoOC').val(totalConcepto('.txtFleteInternoDetalle').toFixed(2));
	/*	var cf1=parseFloat(element.find('.txtCF1Detalle').val());
		$('#txtTotalcostocf1OC').val(totalConcepto('.txtCF1Detalle').toFixed(2));
	var cf2=parseFloat(element.find('.txtCF2Detalle').val());
		$('#txtTotalcostocf2OC').val(totalConcepto('.txtCF2Detalle').toFixed(2));*/
	//element.find('.txtAdvaloremVDetalle').val(advaloremv.toFixed(2));
	//var costosfijos=cif+advaloremv+tasadespacho+fleteinterno+cf1+cf2;
	var costosfijos=cif+advaloremv+tasadespacho+fleteinterno;
	//Costos Variables
	console.log(costosfijos);
	var Flat=parseFloat(element.find('.txtFlatDetalle').val());
		//$('#txtTotalcostoflatOC').val(totalConcepto('.txtFlatDetalle').toFixed(2));
	var VB=parseFloat(element.find('.txtVBDetalle').val());
		//$('#txtTotalcostoalmacenvbOC').val(totalConcepto('.txtVBDetalle').toFixed(2));
	var GateIn=parseFloat(element.find('.txtGateInDetalle').val());
		//$('#txtTotalcostoalmacengateOC').val(totalConcepto('.txtGateInDetalle').toFixed(2));
	/*var BoxFee=parseFloat(element.find('.txtBoxFeeDetalle').val());
		//$('#txtTotalcostoboxfeeOC').val(totalConcepto('.txtBoxFeeDetalle').toFixed(2));
	var InsuranceFee=parseFloat(element.find('.txtInsuranceFeeDetalle').val());
		$('#txtTotalcostoinsurancefeeOC').val(totalConcepto('.txtInsuranceFeeDetalle').toFixed(2));
	var Sobreestadia=parseFloat(element.find('.txtSobreestadiaDetalle').val());
		$('#txtTotalcostosobreestadiaOC').val(totalConcepto('.txtSobreestadiaDetalle').toFixed(2));
	var DocFee=parseFloat(element.find('.txtDocFeeDetalle').val());
		$('#txtTotalcostodocfeeOC').val(totalConcepto('.txtDocFeeDetalle').toFixed(2));
	var GasAdm=parseFloat(element.find('.txtGasAdmDetalle').val());
		$('#txtTotalcostogastosadministrativosOC').val(totalConcepto('.txtGasAdmDetalle').toFixed(2));*/
	var AgenteAduana=parseFloat(element.find('.txtAgenteAduanaDetalle').val());
		//$('#txtTotalcomisionagenteaduOC').val(totalConcepto('.txtAgenteAduanaDetalle').toFixed(2));
	var CV1=parseFloat(element.find('.txtCV1Detalle').val());
		//$('#txtTotalcostocv1OC').val(totalConcepto('.txtCV1Detalle').toFixed(2));
	var CV2=parseFloat(element.find('.txtCV2Detalle').val());
		//$('#txtTotalcostocv2OC').val(totalConcepto('.txtCV2Detalle').toFixed(2));
	var CV3=parseFloat(element.find('.txtCV3Detalle').val());
		//$('#txtTotalcostocv3OC').val(totalConcepto('.txtCV3Detalle').toFixed(2));
	//var costosvariables=Flat+VB+GateIn+BoxFee+InsuranceFee+Sobreestadia+DocFee+GasAdm+AgenteAduana+CV1+CV2;
	var costosvariables=Flat+VB+GateIn+AgenteAduana+CV1+CV2+CV3;
	//calculos TOTALES:
	//console.log(costosfijos);
	//console.log(costosvariables);
	var totalItem=costosfijos+costosvariables;
	console.log(costosvariables);
	var totalunitarioitem=totalItem/cantidad;
	var porcentaje=((totalunitarioitem - fobunitario)/fobunitario)*100;
	element.find('.txtTotalDetalle').val(totalItem.toFixed(2));
	element.find('.txtTotalUnitarioDetalle').val(totalunitarioitem.toFixed(2));
	element.find('.txtPorcentajeDetalle').val(porcentaje.toFixed(2));
	$('#txtTotalimportevalorizadoocOC').val(totalConcepto('.txtTotalDetalle').toFixed(2));
	var totalOC=$('#txtTotalimportevalorizadoocOC').val();
	var tipocambio=$('#tipocambiograbado').val();
	var totalSolesOC=totalOC*tipocambio;
	$('#txtTotalimportevalorizadoocOCA').val($('#txtTotalimportevalorizadoocOC').val());
	$('#txtTotalimportevalorizadoocOCSoles').val(totalSolesOC.toFixed(2));

}

function recalcularxFila(element){

	//Costos Fijos
	var volumenUnitario=parseFloat(element.find('.txtVolumen').val());
	var piezas=parseFloat(element.find('.piezas').val());
	
	var fobunitario=parseFloat(element.find('.txtfobDetalle').val());
	var cantidad=parseFloat(element.find('.txtCantidadDetalle').val());
	var carton=cantidad/piezas;
	var nuevoSubTotalFob=fobunitario*cantidad;
	element.find('.txtfobTotalDetalle ').val(nuevoSubTotalFob.toFixed(2));
	element.find('.carton').val(carton);
	var nuevoCBM=volumenUnitario*carton;
	//element.find('.txtVolumenDetalle').val(nuevoCBM.toFixed(2));

	var flete=parseFloat(element.find('.txtFleteDetalle').val());		
	var seguro=parseFloat(element.find('.txtSeguroDetalle').val());		
	var cif=(fobunitario*cantidad+flete+seguro);

	var cifunitario=(cif/cantidad);
		element.find('.txtciftotal').val(cif.toFixed(2));
		element.find('.txtcifunitario').val(cifunitario.toFixed(2));
	var cifTotalOC=totalConcepto('.txtciftotal');
		$('#txtTotalCifOC').val(cifTotalOC.toFixed(2));

	//var advaloremp=parseFloat(element.find('.txtAdvaloremPDetalle').val());
	//var advaloremv=(cif*advaloremp)/100;
	//element.find('.txtAdvaloremVDetalle').val(advaloremv.toFixed(2));
	advaloremv=parseFloat(element.find('.txtAdvaloremVDetalle').val());
	var advaloremvTotalOC=totalConcepto('.txtAdvaloremVDetalle');
	$('#txtTotaladvaloremOC').val(advaloremvTotalOC.toFixed(2));

	var tasadespacho=parseFloat(element.find('.txtTasaDespachoDetalle').val());
	var costosfijos=cif+advaloremv+tasadespacho;

	//Costos Variables
	var Flat=parseFloat(element.find('.txtFlatDetalle').val());
	var VB=parseFloat(element.find('.txtVBDetalle').val());
	var GateIn=parseFloat(element.find('.txtGateInDetalle').val());
	var AgenteAduana=parseFloat(element.find('.txtAgenteAduanaDetalle').val());
	var fleteinterno=parseFloat(element.find('.txtFleteInternoDetalle').val());
	var CV1=parseFloat(element.find('.txtCV1Detalle').val());
	var CV2=parseFloat(element.find('.txtCV2Detalle').val());
	var CV3=parseFloat(element.find('.txtCV3Detalle').val());
		
	var costosvariables=Flat+VB+GateIn+AgenteAduana+fleteinterno+CV1+CV2+CV3;
	
	var totalItem=costosfijos+costosvariables;
	
	var totalunitarioitem=totalItem/cantidad;
	var porcentaje=((totalunitarioitem - fobunitario)/fobunitario)*100;
	element.find('.txtTotalDetalle').val(totalItem.toFixed(2));
	element.find('.txtTotalUnitarioDetalle').val(totalunitarioitem.toFixed(2));
	element.find('.txtPorcentajeDetalle').val(porcentaje.toFixed(2));
	$('#txtTotalimportevalorizadoocOC').val(totalConcepto('.txtTotalDetalle').toFixed(2));
	var totalOC=$('#txtTotalimportevalorizadoocOC').val();
	var tipocambio=$('#tipocambiograbado').val();
	var totalSolesOC=totalOC*tipocambio;
	$('#txtTotalimportevalorizadoocOCA').val($('#txtTotalimportevalorizadoocOC').val());
	$('#txtTotalimportevalorizadoocOCSoles').val(totalSolesOC.toFixed(2));
}

function totalConcepto(concepto){
	var valorconcepto=0.00;
	var TotalConceptoOC=0.00;
	$(concepto).each(function(){
		valorconcepto=parseFloat($(this).val().replace(',',''));
		TotalConceptoOC += parseFloat(valorconcepto.toFixed(2));
	});
	return TotalConceptoOC;
}
function calcularParteConceptos(concepto,conceptoPorcentaje,totalConceptoPorcentaje){
	valorconcepto=0;
	TotalConceptoOC =totalConcepto(concepto);
	valorConceptoPorcentaje=0;
	var cont=0;
	$(concepto).each(function(){
		valorconcepto=(parseFloat($(this).val().replace(',',''))/TotalConceptoOC)*totalConceptoPorcentaje;
		$(conceptoPorcentaje+':eq('+cont+')').val(valorconcepto.toFixed(2));
		cont++;
	});
	
}
function calcularAdvaloren(){
	valorconcepto=0;
	valorPorcentaje=0;
	valorTotalAdvaloren=$('#txtTotaladvaloremOC').val().replace(',','');
	//console.log(valorTotalAdvaloren);
	var cont=0;
	$('.txtAdvaloremPDetalle').each(function(){
		padre=$(this).parents('tr');
		valorPorcentaje=$('.txtAdvaloremPDetalle'+':eq('+cont+')').val();
		//console.log(valorPorcentaje);
		valorconcepto=((valorTotalAdvaloren*valorPorcentaje)/100);
		console.log(valorconcepto);
		$('.txtAdvaloremVDetalle:eq('+cont+')').val(valorconcepto.toFixed(2));
		cont++;
	});
	
}

function activarConcepto(concepto){
	$(concepto).each(function(){
		//$(this).removeAttr('disabled');
		$(this).removeAttr('readonly');
	});
	var item=$(this).parents('tr');
	//recalcular(item);
}
function desactivarConcepto(concepto){
	$(concepto).each(function(){
		//$(this).attr('disabled','disabled');
		$(this).attr('readonly','readonly').val(0);
		
	});
	var item=$(this).parents('tr');
	//recalcularxFila(item);
}

function modoDesactivado(){
		desactivarConcepto('.txtGateInDetalle');
		desactivarConcepto('.txtVBDetalle');
		desactivarConcepto('.txtFlatDetalle');
		desactivarConcepto('.txtBoxFeeDetalle');
		desactivarConcepto('.txtInsuranceFeeDetalle');
		desactivarConcepto('.txtSobreestadiaDetalle');
		desactivarConcepto('.txtDocFeeDetalle');
		desactivarConcepto('.txtGasAdmDetalle');
		desactivarConcepto('.txtAgenteAduanaDetalle');

}

function desactivaAdicionales(){
		//desactivarConcepto('.txtCF1Detalle');
		//desactivarConcepto('.txtCF2Detalle');
		desactivarConcepto('#txtTotalcostocv1OC');
		desactivarConcepto('#txtTotalcostocv2OC');
		desactivarConcepto('#txtTotalcostocv3OC');
}
