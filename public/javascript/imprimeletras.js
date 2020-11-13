$(document).on('ready', function(){
	$('head').append('<link rel="stylesheet" href="/css/letras.css">');
	$('head').append('<link rel="stylesheet" href="/css/print-letras.css" media="print">');
	$('#print').on('click',function(e){
		e.preventDefault();
		window.print();
		//window.location='/documento/listaDocumentos/';
	});

});