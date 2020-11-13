$(document).ready(function(){

	var lstZona=$('#lstZona').html();
	var lstRegionCobranza=$('#lstRegionCobranza').html();
	var lstProvincia=$('#lstProvincia').html();
	var lstDistrito=$('#lstDistrito').html();
	
	/*********************/
	cargaDpt();
	/**************** Botones **********************/
        
        
        $('#btnNuevaVistaCliente').click(function(e){
		//e.preventDefault();
		window.location = '/reporte/reporteVentasCliente/';
                console.log('listo');
	});
        
        $('#btnConsultar').click(function () {
            $(this).attr('disabled', ' disabled');
            $('#carteraCliente').html("<center><img src='/public/imagenes/cargando.gif' width='300'></center>");
            $.ajax({
                url: "/reporte/reporteCarteraClientes_seleccion",
                data: {'idVendedor': $('#idVendedor').val(), 'lstCondicion': $('#lstCondicion').val(), 'lstCategoriaPrincipal': $('#lstCategoriaPrincipal').val(), 'lstRegionCobranza': $('#lstRegionCobranza').val(), 'lstZona': $('#lstZona').val(), 'txtFechaInicio': $('#txtFechaInicio').val(), 'txtFechaFin': $('#txtFechaFin').val(), 'lstMostrar': $('#lstMostrar').val(), 'lstOrden': $('#lstOrden').val(), 'aprobados': $('#lstAprobados').val()},
                type: "POST",
                success: function (datos) {
                    $('#carteraCliente').html(datos);
                    $('#btnConsultar').removeAttr('disabled');
                },
                error: function (a, b, c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                }
            });
        });
        
        $('#btnConsultarPedidoVentas1').click(function (e) {
            //$(this).attr('disabled', ' disabled');
            $('#carteraCliente').html("<center><img src='/public/imagenes/cargando.gif' width='300'></center>");
            $.ajax({
                url: "/reporte/reporteConsultaPedidoVentas1",
                //data: {'idVendedor': $('#idVendedor').val(), 'lstCondicion': $('#lstCondicion').val(), 'lstCategoriaPrincipal': $('#lstCategoriaPrincipal').val(), 'lstRegionCobranza': $('#lstRegionCobranza').val(), 'lstZona': $('#lstZona').val(), 'txtFechaInicio': $('#txtFechaInicio').val(), 'txtFechaFin': $('#txtFechaFin').val(), 'lstMostrar': $('#lstMostrar').val(), 'lstOrden': $('#lstOrden').val(), 'aprobados': $('#lstAprobados').val()},
                data: {'idVendedor': $('#idVendedor').val(), 'txtFechaInicio': $('#txtFechaInicio').val(), 'txtFechaFin': $('#txtFechaFin').val(), 'lstProvinciax': $('#lstprovinciax').val()},
                type: "POST",
                success: function (datos) {
                    console.log(datos);
                    $('#carteraCliente').html(datos);
                    //$('#btnConsultar').removeAttr('disabled');
                },
                error: function (a, b, c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                }
            });
        });
        
        $('#btnConsultarExcelPedidoVentas').click(function(e){
		if(confirm("¿Desea exportar en excel?")){
                    $('#frmParametros').attr('action','/excel/DescargarExcelPedidoVentas1');
                }else{
                    e.preventDefault();
                }
	});
        
        $('#btnSelPDF').live('click', function () {
            $('#form-seleccion').attr('action', '/pdf/reporteCarteraClientes');
        });
        
        $('#btnSelExcel').live('click', function () {
           $('#form-seleccion').attr('action', '/excel/reporteCarteraClientes');
        });
        /*
	$('#btnConsultarHtml5').click(function(e){
		e.preventDefault();
                
                 $('#carteraCliente').html('');
                 $('#carteraCliente').html('<th style="text-align: center;"><img style="width:250px;heigth:100" src="/imagenes/cargando.gif"></th>');
		cargaConsulta('');
		//console.log($('#Parametros').serialize());
               
	});*/
        $('.btnGrabar').live('click',function(e){
		e.preventDefault();
		padre=$(this).parents('tr');
                idcliente=$(this).attr('rel');
               
		if (confirm("Esta seguro de Grabar")) {
                    var validacion=verificarCliente(idcliente,padre);
			if (validacion==true) {
                            grabarCrediticia(idcliente,padre);	
                            
				
			}else{
			    updateCrediticia(idcliente,padre);
			}
		 
		}		
	});
	$('#btnLimpiar').click(function(e){
		e.preventDefault();
		limpiar();
	});
	$('#btnImprimirHtml5').click(function(e){
		e.preventDefault();
		imprSelec('contenedorImpresion');
		
	});

	$('#btnConsultarPdf').click(function(e){
		$('#frmParametros').attr('action','/pdf/reporteCarteraClientes');
	});
        
        $('#btnResumen').click(function(e){
		//alert(44);
		$('#frmParametros').attr('action','/excel/reporteCarteraClientesResumen');	
	});
        
        $('#btnFormatoExcel').click(function(e){
		//alert(44);
		$('#frmParametros').attr('action','/excel/reporteCarteraClienteFormatoExcel');	
	});
        
	$('#btnConsultarExcel').click(function(e){
		//alert(44);
		$('#frmParametros').attr('action','/excel/reporteCarteraClientes');	
	});
        
        $('#btnConsultarPorZona').click(function(e){
		//alert(44);
		$('#frmParametros').attr('action','/pdf/reporteCarteraClientesxzona');	
	});
        
        $('#btnConsultarPorVendedor').click(function(e){
            if ($('#idVendedor').val() == '') {
                $('#txtVendedor').focus();
                return false;
            } else {
                $('#frmParametros').attr('action','/pdf/reporteCarteraClientesxvendedor');
            }	
	});
        
        $('#btnClientesPorVendedor').click(function(e){
            if ($('#idVendedor').val() == '') {
                $('#txtVendedor').focus();
                return false;
            } else {
                $('#frmParametros').attr('action','/pdf/reporteClientesxvendedor');
            }	
	});

	
        $('#btnRankingClientexVendedor').click(function(e){
            if ($('#idVendedor').val() == '') {
                $('#txtVendedor').focus();
                return false;
            } else {
                $('#frmParametros').attr('action','/excel2/RankingClientesxVendedor');
            }	
	});
        
        $('#chkTodo').live('click', function () {
            if ($(this).prop('checked')) {
                $('.chkzona').prop('checked', true);
            } else {
                $('.chkzona').prop('checked', false);
            }
        });
	
	/**************  Listas ***********/
	$('#lstCategoriaPrincipal').change(function(){
		idpadre=$(this).val();
		if (idpadre=="") {
			$('#lstRegionCobranza').html(lstRegionCobranza);
			$('#lstRegionCobranza').change();
		}else{
			cargaRegionCobranza(idpadre);
		}

		$('#lstRegionCobranza').change();
	});

	$('#lstRegionCobranza').change(function(){
		idzona=$(this).val();
		//console.log(idzona);
		if (idzona=="") {
			$('#lstZona').html(lstZona);
		}else{
			cargaZonas(idzona);
		}
	});

	$('#lstZona').change(function(){

	});
	
	$('#lstDepartamento').change(function(){
		if($('#lstDepartamento').val()==""){
			$('#lstProvincia').html(lstProvincia);
			$('#lstProvincia').change();
		}
	});

	$('#lstProvincia').change(function(){
		if($('#lstProvincia').val()==""){
			$('#lstDistrito').html(lstDistrito);
		}		
	});

	/******************** Autocomplete ****************************/
	$('#txtOrdenVenta').autocomplete({
		source: "/ordenventa/buscarautocompletecompleto/",
		select: function(event, ui){
			$('#idOrdenVenta').val(ui.item.id);
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
        
        $('#txtVendedor').focusout(function(){
            if ($(this).val()=='') {
                $('#idVendedor').val('');
            }
        });
	
    //});
});
function verificarCliente(idcliente){
	var verificacion=true;
	
	$.ajax({
		url:'/reporte/verificarCliente',
		type:'post',
		dataType:'json',
		async:false,
		data:{'idcliente':idcliente},
		success:function(resp){
			console.log(resp);
			if (resp.exito==false) {
				verificacion=false;
			}
		}
	});

	return verificacion;
}
//funciones fuera del evento ready
function updateCrediticia(idcliente,padre){
        var lineacrediticia=padre.find('.lineacrediticia').val();
	var calificacion=padre.find('.calificacion').val();
	var condicion=padre.find('.condicion').val();
    $.post('/reporte/updateCrediticia/',{idcliente:idcliente,lineacrediticia:lineacrediticia,calificacion:calificacion,condicion:condicion},function(data){
        
        
    });
    
}
function grabarCrediticia(idcliente,padre){
        var lineacrediticia=padre.find('.lineacrediticia').val();
	var calificacion=padre.find('.calificacion').val();
	var condicion=padre.find('.condicion').val();
    $.post('/reporte/grabarCrediticia/',{idcliente:idcliente,lineacrediticia:lineacrediticia,calificacion:calificacion,condicion:condicion},function(data){
        
        
    });
    
}
function cargaRegionCobranza(idpadre){
	$.ajax({
		url:'/zona/listaCategoriaxPadre',
		type:'post',
		async: false,
		dataType:'html',
		data:{'idpadrec':idpadre},
		success:function(resp){
			//console.log(resp);
			$('#lstRegionCobranza').html(resp);
		}
	});
}
function cargaZonas(idzona){
	$.ajax({
		url:'/zona/listaZonasxCategoria',
		type:'post',
		async: false,
		dataType:'html',
		data:{'idzona':idzona},
		success:function(resp){
			$('#lstZona').html(resp);
		}
	});
}
function cargaDpt(){
	$.ajax({
		url:'/departamento/listaroptions/pe',
		type:'post',
		async: false,
		dataType:'html',
		success:function(resp){
			$('#lstDepartamento').html("<option value=''>Seleccione Departamento</option>"+resp);
		}
	});
}
function cargaProv(iddepartamento){
	$.ajax({
		url:'/provincia/listaroptions/'+iddepartamento,
		type:'post',
		async: false,
		dataType:'html',
		success:function(resp){
			$('#lstDepartamento').html("<option value=''>Seleccione Provincia</option>"+resp);
		}
	});
}
function cargaDist(){
	$.ajax({
		url:'/departamento/listaroptions/pe',
		type:'post',
		async: false,
		dataType:'html',
		success:function(resp){
			$('#lstDepartamento').html("<option value=''>Seleccione Departamento</option>"+resp);
		}
	});
}
function cargaConsulta(numpag){
    $.ajax({
        url:'/reporte/reporteCarteraCobranza/'+numpag,
        type:'post',
        dataType:'html',
        data:$('#frmParametros').serialize(),
        success:function(resp){
                $('#carteraCliente').html(resp);
        }
    });
}

function limpiar(){
	$('#frmParametros')[0].reset();
	$('.encabezado').html('');
	$('#idOrdenVenta').val('');
	$('#idCliente').val('');
	$('#idVendedor').val('');
	$('#cabecera').hide();
	$('#tblVentas').hide();
}
function llenarEncabezado(){
	$('#lblFechaGuiado').html($('#txtFechaGuiadoInicio').val()+' - '+$('#txtFechaGuiadoFin').val());
	$('#lblFechaAprobado').html($('#txtFechaAprobadoInicio').val()+' - '+$('#txtFechaAprobadoFinal').val());
	$('#lblFechaDespacho').html($('#txtFechaDespachoInicio').val()+' - '+$('#txtFechaDespachoFin').val());
	$('#lblFechaCancelado').html($('#txtFechaCanceladoInicio').val()+' - '+$('#txtFechaCanceladoFin').val());
	$('#lblOrdenVenta').html($('#txtOrdenVenta').val());
	$('#lblCliente').html($('#txtCliente').val());
	$('#lblVendedor').html($('#txtVendedor').val());
	$('#lblZonaGeografica').html($('#lstCategoriaPrincipal option:selected').html());
	$('#lblRegionCobranza').html($('#lstRegionCobranza option:selected').html());
	$('#lblZona').html($('#lstZona option:selected').html());
	$('#lblCondicion').html($('#lstCondicion').val());
}

