$(document).ready(function(){
 $("#updateresumen").on("click",function(){
    if ($(this).attr('checked') ) {
        $(this).attr('value',1);
    }else{
        $(this).attr('value',0);
    }
 });
    
    var tcambioCompra=0;
    $.ajax({
        url:'/tipocambio/obtenerTipoCambio',
        type:'post',
        async: false,
        dataType:'json',
        success:function(resp){
            tcambioCompra=resp.resultado;
        }
    });
    
    var lstCategoriaPrincipal=$('#lstCategoriaPrincipal').html();
    var lstZonaCobranza=$('#lstZonaCobranza').html();
    var lstZona=$('#lstZona').html();
   $('.desplegar').live("click",function(){
        
        filastabla="";
        variables=$(this).attr("value");
        data=variables.split("|");
        tabla=data[0];
        fila=data[1];
        cliente=data[2];
        indice=data[3];
        
        desplegado=$("#"+fila).attr('value');
        if(desplegado=="1"){ $("#"+tabla+" .bitacora").remove();  $("#"+fila).attr('value','0'); }
        if(desplegado=="0"){
        $("#"+fila).remove();
        
        $.ajax({
                url:'/creditos/historialcredito2',
                type:'post',
                async: false,
                dataType:'json',
                data:'idcliente='+cliente,
                success:function(resp){
                    if (resp.resultado!="noauditado"){
                        $.each(resp, function (i, val) {
                            if(val['motivo']=="credito"){
                                condicioncompra=val['condicioncompra'];
                                if(condicioncompra==null){ condicioncompra=''; }
                                lcreditoBitacora=0.00;
                                deudaBitacora=0.00;
                                lcreditoDisponibleBitacora=0.00;
                                info='';
                                lcreditoBitacora=parseFloat(val['lcreditosoles'])+(parseFloat(val['lcreditodolares'])*parseFloat(tcambioCompra));
                                deudaBitacora=parseFloat(val['deudasoles'])+(parseFloat(val['deudadolares'])*parseFloat(tcambioCompra));
                                //lcreditoDisponibleBitacora=lcreditoBitacora-deudaBitacora;
                              if(val['movimiento']==1){ info="ampliaron"; }
                              if(val['movimiento']==2){ info="disminuyeron"; }

                                filastabla +="<tr class='bitacora' style='text-align:center !important;background-color: #80808029;'>";
                                filastabla += "<td class='centro1'><input disabled type='text' class='cajitasIzqJS' value='S/."+val['dcontado_s']+"'/><input disabled type='text' class='cajitasDerJS'  value='$. "+val['dcontado_d']+"'/></td>";
                                filastabla += "<td class='centro1'><input disabled type='text' class='cajitasIzqJS' value='S/."+val['dcredito_s']+"'/><input disabled type='text' class='cajitasDerJS'  value='$. "+val['dcredito_d']+"'/></td>";
                                filastabla += "<td class='centro1'><input disabled type='text' class='cajitasIzqJS' value='S/."+val['dletrabanco_s']+"'/><input disabled type='text' class='cajitasDerJS'  value='$. "+val['dletrabanco_d']+"'/></td>";
    //                            filastabla += "<td class='centro1'><input disabled type='text' class='cajitasIzqJS CarteraJS' value='S/."+val['dletracartera_s']+"'/><input disabled type='text' class='cajitasDerCarteraJS' value='$. "+val['dletracartera_d']+"'/></td>";
                                filastabla += "<td class='centro1' style='font-size:10px;'>"+condicioncompra+"</td>";
                                filastabla += "<td class='centro1'><input disabled type='text' class='cajitasIzqJS' value='S/."+val['dletraprotestada_s']+"'/><input disabled type='text' class='cajitasDerJS'  value='$. "+val['dletraprotestada_d']+"'/></td>";
                                filastabla += "<td class='centro1'>S/. "+(Math.round(lcreditoBitacora*100)/100).toFixed(2)+"</td>";
                                filastabla += "<td class='centro1'>S/. "+(Math.round(deudaBitacora*100)/100).toFixed(2)+"</td>";
                                filastabla += "<td class='centro1' style='font-size:11.5px;'>"+info+"&nbsp;&nbsp;&nbsp;$."+val['cantidad']+"</td>";
                                filastabla += "<td class='centro1' style='font-size:10px;'>"+val['calificacion']+"</td>";
                                filastabla += "<td class='centro1'><input disabled type='text' id='txtObservacion' value='"+val['observaciones']+"'/></td>";
                                filastabla += "</tr>";
                                condicioncompra='';  
                            }
                            
                            if(val['motivo']=="condiciones"){
                                condicioncompra=val['condicioncompra'];
                                if(condicioncompra==null){ condicioncompra=''; }
                                lcreditoBitacora=0.00;
                                deudaBitacora=0.00;
                                lcreditoDisponibleBitacora=0.00;
                                info='';
                                lcreditoBitacora=parseFloat(val['lcreditosoles'])+(parseFloat(val['lcreditodolares'])*parseFloat(tcambioCompra));
                                deudaBitacora=parseFloat(val['deudasoles'])+(parseFloat(val['deudadolares'])*parseFloat(tcambioCompra));
                                //lcreditoDisponibleBitacora=lcreditoBitacora-deudaBitacora;
                              if(val['movimiento']==1){ info="ampliaron"; }
                              if(val['movimiento']==2){ info="disminuyeron"; }

                                filastabla +="<tr class='bitacora' style='text-align:center !important;background-color: #80808029;'>";
                                filastabla += "<td class='centro1'></td>";
                                filastabla += "<td class='centro1'></td>";
                                filastabla += "<td class='centro1'></td>";
    //                            filastabla += "<td class='centro1'><input disabled type='text' class='cajitasIzqJS CarteraJS' value='S/."+val['dletracartera_s']+"'/><input disabled type='text' class='cajitasDerCarteraJS' value='$. "+val['dletracartera_d']+"'/></td>";
                                filastabla += "<td class='centro1' style='font-size:10px;'>"+condicioncompra+"</td>";
                                filastabla += "<td class='centro1'></td>";
                                filastabla += "<td class='centro1'></td>";
                                filastabla += "<td class='centro1'></td>";
                                filastabla += "<td class='centro1' style='font-size:11.5px;'></td>";
                                filastabla += "<td class='centro1' style='font-size:10px;'>"+val['calificacion']+"</td>";
                                filastabla += "<td class='centro1'><input disabled type='text' id='txtObservacion' value='"+val['observaciones']+"'/></td>";
                                filastabla += "</tr>";
                                condicioncompra='';  
                            }
                            
                        });
                    }
                }
            });
            $('#'+tabla).append(filastabla);
            $('#'+tabla).append("<tr id='"+fila+"' value='1' rowspan='3' style='color: #80808029; background-color: #80808029;'><td colspan=10>&nbsp;sssssssas<br><br><br></td></tr>");
        }
    }); 
        
    $('.btnGrabar').live("click",function(){
        indice=$(this).attr("value");
        modificandolineacredito=$("#txtCantidad"+indice).attr("value");
        if(modificandolineacredito>0){
             grabar=0;
                cmbCalificacion=$("#cmbCalificacion"+indice).attr("value");
                cmbCondicionCompra=$("#cmbCondicionCompra"+indice).attr("value");

                if(cmbCalificacion!="" && cmbCondicionCompra!=""){  grabar=1;
                }else{  alert("Seleccione  Condicion de Compra y Calificacion"); grabar=0; }
                if(grabar==1){
                    variables=$("#btnVer"+indice).attr("value");
                    data=variables.split("|");
                    idcliente=data[2];
                    cmbMovimiento=$("#cmbMovimiento"+indice).attr("value");
                    txtCantidad=$("#txtCantidad"+indice).attr("value");
                    txtObservacion=$("#txtObservacion"+indice).attr("value");
                    txtdcontado_s=$("#txtdcontado_s"+indice).attr("value").replace("S/.","");
                    txtdcontado_d=$("#txtdcontado_d"+indice).attr("value").replace("$. ","");
                    txtdcredito_s=$("#txtdcredito_s"+indice).attr("value").replace("S/.","");
                    txtdcredito_d=$("#txtdcredito_d"+indice).attr("value").replace("$. ","");
                    txtdletrabanco_s=$("#txtdletrabanco_s"+indice).attr("value").replace("S/.","");
                    txtdletrabanco_d=$("#txtdletrabanco_d"+indice).attr("value").replace("$. ","");
                    txtdletracartera_s=$("#txtdletracartera_s"+indice).attr("value").replace("S/.","");
                    txtdletracartera_d=$("#txtdletracartera_d"+indice).attr("value").replace("$. ","");
                    txtdletraprotestada_s=$("#txtdletraprotestada_s"+indice).attr("value").replace("S/.","");
                    txtdletraprotestada_d=$("#txtdletraprotestada_d"+indice).attr("value").replace("$. ","");
                    txtLineaCredito_soles=$('#txtLineaCredito'+indice).attr("value").replace("S/.","");
                    variables = 'idcliente=' + idcliente+'&cmbCalificacion='+cmbCalificacion+'&cmbMovimiento='+cmbMovimiento;
                    variables += '&txtCantidad=' + txtCantidad+'&txtObservacion='+txtObservacion+'&txtdcontado_s='+txtdcontado_s;
                    variables += '&txtdcontado_d=' + txtdcontado_d+'&txtdcredito_s='+txtdcredito_s+'&txtdcredito_d='+txtdcredito_d;
                    variables += '&txtdletrabanco_s=' + txtdletrabanco_s+'&txtdletrabanco_d='+txtdletrabanco_d;
                    variables += '&txtdletracartera_s='+txtdletracartera_s+'&txtdletracartera_d=' + txtdletracartera_d;
                    variables += '&txtdletraprotestada_s='+txtdletraprotestada_s+'&txtdletraprotestada_d='+txtdletraprotestada_d;
                    variables += '&cmbCondicionCompra='+cmbCondicionCompra+'&txtLineaCredito_soles='+txtLineaCredito_soles;
                    $.ajax({
                        url:'/creditos/grabarLineaCredito',
                        type:'get',
                        async: false,
                        dataType:'json',
                        data:variables,
                        success:function(resp){

                           if(resp.resultado==1){
                               alert("Grabado Correctamente");
                           }else{
                               alert("     1Error en la actualizacion, vuelva a recargar \n Si el error persiste consulte con el area de sistemas");
                           }
                        },error: function () {
                        alert('El servidor no responde  sss');
                        }
                    });

                    $.ajax({
                        url:'/creditos/calcularCreditoDisponible',
                        type:'get',
                        async: false,
                        dataType:'json',
                        data:'idcliente=' + idcliente,
                        success:function(resp){
                            $.each(resp, function (i) {
                                  lineacreditoactual=(Math.round(resp[i].lineacreditoactual*100)/100).toFixed(2);
                                  deudatotal=(Math.round(resp[i].deudatotal*100)/100).toFixed(2);
                                  lineacreditodisponible=lineacreditoactual-deudatotal;
                                  lineacreditodisponible=(Math.round(lineacreditodisponible*100)/100).toFixed(2);
                                  $('#txtLineaCredito'+indice).attr('value','S/. '+lineacreditoactual);
                                  $('#txtDeudaTotal'+indice).attr('value','S/. '+deudatotal);
                                  $('#txtLineaCreditoDisponible'+indice).attr('value','S/. '+lineacreditodisponible);
                            });
                        }
                    });

                    $('#txtCantidad'+indice).attr('value','');
                    $('#txtObservacion'+indice).attr('value','');
                    //$("#cmbMovimiento"+indice+" option[value='1']").attr("selected",true);
                    $('#btnVer'+indice).trigger("click"); 
                }
        }else{
            
            grabar=0;
            variables_pk=$("#btnVer"+indice).attr("value");
            data=variables_pk.split("|");
            idcliente=data[2];
            cmbCondicionCompra=$("#cmbCondicionCompra"+indice).attr("value");
            cmbCalificacion=$("#cmbCalificacion"+indice).attr("value");
            txtObservacion=$("#txtObservacion"+indice).attr("value");
            url_variables = 'idcliente=' + idcliente+'&cmbCondicionCompra=' + cmbCondicionCompra+'&cmbCalificacion='+cmbCalificacion+'&txtObservacion='+txtObservacion;
            if(cmbCalificacion!="" && cmbCondicionCompra!=""){  grabar=1;
            }else{  alert("Seleccione  Condicion de Compra y Calificacion"); grabar=0; }
            
            
           if(grabar==1){ 
            $.ajax({
                        url:'/creditos/grabarClienteObservaciones',
                        type:'get',
                        async: false,
                        dataType:'json',
                        data:url_variables,
                        success:function(resp){

                           if(resp.resultado==1){
                               alert("Grabado Correctamente");
                           }else{
                               alert("     2Error en la actualizacion, vuelva a recargar \n Si el error persiste consulte con el area de sistemas");
                           }
                        },error: function () {
                        alert('2 El servidor no responde');
                        }
                    });
            $('#txtObservacion'+indice).attr('value','');
            //$("#cmbMovimiento"+indice+" option[value='1']").attr("selected",true);
            $('#btnVer'+indice).trigger("click");    
            }

            
        }
    });    
        
    $('#lstZonaCobranza').change(function(){
        idzona=$(this).val();
        
        if (idzona=="") {
            $('#lstZona').html(lstZona);
        }else{
            cargaZonas(idzona);
        }
        $('#lstZona').change();
    });
    $('#lstCategoriaPrincipal').change(function(){
        idpadre=$(this).val();
        if (idpadre=="") {
            $('#lstZonaCobranza').html(lstZonaCobranza);
            $('#lstZonaCobranza').change();
        }else{
            cargaRegionCobranza(idpadre);
        }

        $('#lstZonaCobranza').change();
        
    });
    /********************** Botones *****************************/
    $('#btnLimpiar').click(function(e){
        e.preventDefault();
        limpiar();
    });
    $('#btnConsultar').click(function(e){
        e.preventDefault();
        txtCliente=$('#txtCliente').attr("value");
        idCliente=$('#idCliente').attr("value");
        txtVendedor=$('#txtVendedor').attr("value");
        idVendedor=$('#idVendedor').attr("value");
        
        lstCategoriaPrincipal=$('#lstCategoriaPrincipal').attr("value");
        lstZonaCobranza=$('#lstZonaCobranza').attr("value");
        if(idCliente!="" || txtCliente!="" && idVendedor=="" && txtVendedor=="" && lstCategoriaPrincipal=="" && lstZonaCobranza==""){
            consulta(); //busca por cliente
        }
        if(idVendedor!="" || txtVendedor!="" && idCliente=="" || txtCliente=="" && lstCategoriaPrincipal=="" && lstZonaCobranza==""){
            consulta(); //busca por vendedor
        }
        if(idCliente=="" && txtCliente=="" && idVendedor=="" && txtVendedor==""){
            if(lstCategoriaPrincipal=="" && lstZonaCobranza==""){
               alert("Seleccione la Zona Geografica");
            }
            if(lstCategoriaPrincipal!="" && lstZonaCobranza==""){
               alert("Seleccione la Zona Cobranza Categoria");
            }
            if(lstCategoriaPrincipal!="" && lstZonaCobranza!=""){
               consulta();
            }
        }
    });
    
    $('#lstCategoriaPrincipal').on('click change',function(){
       $('#txtCliente').attr("value","");
       $('#idCliente').attr("value","");
       $('#txtVendedor').attr("value","");
       $('#idVendedor').attr("value","");
    });
    $('#lstZonaCobranza').on('click change',function(){
       $('#txtCliente').attr("value","");
       $('#idCliente').attr("value","");
       $('#txtVendedor').attr("value","");
       $('#idVendedor').attr("value","");
    });
     $('#txtCliente').on('keyup',function(){
       $("#lstCategoriaPrincipal option[value='']").attr("selected",true);
       $("#lstZonaCobranza option[value='']").attr("selected",true);
       $("#lstZona option[value='']").attr("selected",true);
       $('#txtVendedor').attr("value","");
       $('#idVendedor').attr("value","");
    });
   $('#txtVendedor').on('keyup',function(){
       $("#lstCategoriaPrincipal option[value='']").attr("selected",true);
       $("#lstZonaCobranza option[value='']").attr("selected",true);
       $("#lstZona option[value='']").attr("selected",true);
       $('#txtCliente').attr("value","");
       $('#idCliente').attr("value","");
    });
  
    
    $('#btnImprimir').click(function(e){
        e.preventDefault();
        $('.spanGrabar').empty();
        $('.desplegar').empty();

        imprSelec('tblCuerpo');
        
    });
    /******************** Autocomplete ****************************/
    $('#txtCliente').autocomplete({
        source: "/cliente/autocomplete2/",
        select: function(event, ui){
            $('#idCliente').val(ui.item.id);
        }
    });

    $('#txtVendedor').autocomplete({
        source: "/vendedor/autocompletevendedor/",
        select: function(event, ui){
            $('#idVendedor').val(ui.item.id);
        }
    });
});
function cargaRegionCobranza(idpadre){
    $.ajax({
        url:'/zona/listaCategoriaxPadre',
        type:'post',
        async: false,
        dataType:'html',
        data:{'idpadrec':idpadre},
        success:function(resp){
            $('#lstZonaCobranza').html(resp);
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
function consulta(){
 $.ajax({
        url:'/creditos/listadoEvaluacionCrediticia',
        type:'post',
        async: false,
        dataType:'html',
        data:$('#frmCobranza').serialize(),
        success:function(resp){
            $('#tblCuerpo').html('');
            $('#tblCuerpo').html(resp).show();
            
        }
    });
}

