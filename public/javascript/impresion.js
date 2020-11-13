$(document).on('ready', function(){
	$('head').append('<link rel="stylesheet" href="/css/factura.css">');
	$('head').append('<link rel="stylesheet" href="/css/print-factura.css" media="print">');
	$('#print').on('click',function(e){
		e.preventDefault();
		window.print();
		window.close();
	});

});
	


function imprSelec(muestra)
{

	var ficha=document.getElementById(muestra);
	var ventimp=window.open(' ','popimpr');
	ventimp.document.write(ficha.innerHTML);
	ventimp.document.close();ventimp.print();
	ventimp.close();
}

