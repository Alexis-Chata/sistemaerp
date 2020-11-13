jQuery.msgbox = function(title, content, buttons){
	buttons = buttons || '<a href="#" id="msgbox-ok">Aceptar</a>';
	$("body").append('<div id="bgTransparent"></div>');
    $(window).resize();
    $("body").append('<div id="msgbox">' + 
        '<div id="msgbox-header">' + 
            '<span id="msgbox-title">' + title + '</span>' + 
            '<a href="#" id="msgbox-close" title="Cerrar">x</a>' + 
        '</div>' + 
        '<div id="msgbox-content">' + content + '</div>' + 
        '<div id="msgbox-buttons">' + buttons + '<a href="" id="msgbox-cancel" title="Cancelar">Cancelar</a></div>' + 
    '</div>');
    execute();
    $('#msgbox-ok').focus();
    $("#msgbox-close, #msgbox-cancel, #msgbox-ok").click(function(e){
        e.preventDefault();
       	$("#msgbox, #bgTransparent").remove();
    });
};
jQuery.modalBox = function(contents){
    $("body").append('<div id="bgTransparent"></div>');
    $("body").append('<div id="modalBox"><a href="#" id="closeModalBox">x</a></div>');
    $("#closeModalBox").click(function(e){
        e.preventDefault();
       $("#modalBox").remove();
       $("#bgTransparent").remove();
    });
    $("#modalBox").prepend(contents + "<br>");
    $(window).resize();
};
$(window).resize(function(){
    var dHeight = $(document).height();
    var dWidth = $(document).width();
    $("#bgTransparent").css({"height":dHeight + "px", "width":dWidth + "px"});
});

jQuery.fn.lightBox = function(){
    this.each(function(){
        var element = $(this);
        element.click(function(e){
            e.preventDefault();
           $.modalBox(element.html()); 
        });
    });
    return this;
};

jQuery.fn.valida = function(){
	var x = 0;
	var y = 0;
	exito = 1;
	var element = $(this);
	//alert(element);
	element.each(function(){
		x+=1;
		y+=1;
	});
	element.each(function(){
		$(this).next('span.error').remove();
		if($(this).val().length == 0){
			$(this).after('<span class="error">*</span>');
			x-=1
		}else{
			$(this).next('span.error').remove();
		}
	});
	if(x != y){
		$.msgbox('Error','Ingrese todos los datos requeridos.');

		execute();
		return false;
	}else{
		return true;
	}
};

jQuery.fn.maxWidth = function(){
	var maxWidth = 0;
	this.each(function(){
		if($(this).width() > maxWidth){
			maxWidth = $(this).width();
		}
	})
	return maxWidth;
}

jQuery.fn.exactWidth = function(){
	var element = $(this);
	element.each(function(){
		if(!$(this).attr('maxlength')){
			var width = parseInt($(this).val().length) + 10;
			$(this).attr('size',width);	
		}
	});
	return this;
}

jQuery.fn.resetForm = function(){
	$(this).each (function(){
		this.reset();
	});
}

function execute(){
    var marginTop = "-" + ($("#msgbox").height() / 2) + "px";
    var marginLeft = "-" + ($("#msgbox").width() / 2) + "px";
    $("#msgbox").css({"margin-top":marginTop, "margin-left":marginLeft});
}