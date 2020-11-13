$(document).ready(function(){
	MuestraTipoCambioVigente();
	$('#tblTipoCambioHoy').show();
	$('#txtvalorventa').hide();
	$('#txtMoneda').change(function(){
		var idMoneda=$('#txtMoneda').val();
		SimboloMoneda(idMoneda);
		if(idMoneda>1){
			$('#txtvalorventa').show();
			var tipocambio=TipoCambio(idMoneda);
			$('#txtvalorventa').val(tipocambio);
			$('#txtvalorventa').val(tipocambio);
		}else{
			$('#txtTipoCambioValor').val("1.00");
			$('#txtidTipoCambio').val("0");
			$('#txtvalorventa').hide();
		}
	});

});

function TipoCambio(idMoneda){
	var url="/tipocambio/consulta/"+idMoneda;
	$.getJSON(url,function(data){
		$('#txtvalorventa').val("Venta: "+data.venta);
		$('#txtvalorventa').attr("disabled","disabled");
		$('#txtidTipoCambio').val(data.idtipocambio);
		$('#txtTipoCambioValor').val(data.venta);
	});
}

function TipoCambioVenta(idmoneda){
	var url="/tipocambio/consulta/"+idmoneda;
	$.getJSON(url,function(data){
		$('#tipocambioventadolar').val(data.venta);
	});
	
}

function SimboloMoneda(idmoneda){
	var url="/tipocambio/consultaSimboloVigente/"+idmoneda;
	$.getJSON(url,function(data){
		$('#lblMoneda').val(data.simbolo);
	});
	
}

function MuestraTipoCambioVigente(){
	 TipoCambioVenta(2);
	var url="/tipocambio/consultahoy/";
		$.post(url,function(data){
			if(data==0){
				alert("El Sistema no tiene tipo de cambio vigente");
				$('#tblTipoCambio tbody').html("<h2>No hay tipo de cambio asignado para HOY</h2>");
			}else{
				$('#tblTipoCambio tbody').html(data);
			}
		
	});
}
