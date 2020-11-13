$(document).on('ready', function(){
	$('head').append('<link rel="stylesheet" href="/css/guia.css">');
	$('head').append('<link rel="stylesheet" href="/css/print-guia.css" media="print">');
	$('#print').on('click',function(e){
		e.preventDefault();
		window.print();
		window.close();
	});

});