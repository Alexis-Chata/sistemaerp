<?php

Class creditoscontroller extends ApplicationGeneral {

    function AsignaPagos() {
        $_SESSION['Autenticado'] = true;
        $this->view->show("creditos/form.phtml");
    }

    function retornarVentas() {
        $idordenventa = $_REQUEST['idordenventa'];
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $detalleordenventa = $this->AutoLoadModel('detalleordenventa');
        $producto = $this->AutoLoadModel('producto');
        $data['mventas'] = 'RETORNADO POR COBRANZAS';
        $data['mcobranzas'] = '';
        $data['malmacen'] = '';
        $data['vbventas'] = '-1';
        $data['vbalmacen'] = '-1';
        $data['vbcobranzas'] = '-1';
        $exito = $ordenventa->actualizaOrdenVenta($data, $idordenventa);
        if ($exito) {
            $dataOrdenVenta = $detalleordenventa->listaDetalleOrdenVentaYOrden($idordenventa);
            $cantidad = count($dataOrdenVenta);
            for ($i = 0; $i < $cantidad; $i++) {
                $cantsolicitada = $dataOrdenVenta[$i]['cantsolicitada'];
                $cantidaddespachada = $dataOrdenVenta[$i]['cantdespacho'];
                $idproducto = $dataOrdenVenta[$i]['idproducto'];
                $iddetalleordenventa = $dataOrdenVenta[$i]['iddetalleordenventa'];
                $dataProducto = $producto->buscaProducto($idproducto);
                $stockdisponible = $dataProducto[0]['stockdisponible'];
                $nuevoStockDisponible = $stockdisponible + $cantidaddespachada - $cantsolicitada;
                $datos['stockdisponible'] = $nuevoStockDisponible;
                $actualizar = $producto->actualizaProducto($datos, $idproducto);
            }
            if ($actualizar) {
                $datoOV['cantdespacho'] = 0;
                $datoOV['tipodescuento'] = 0;
                $datoOV['cantaprobada'] = 0;
                $datoOV['precioaprobado'] = 0;
                $datoOV['descuentoaprobado'] = 0;
                $datoOV['descuentoaprobadovalor'] = 0;
                $datoOV['descuentoaprobadotexto'] = '';
                $exito2 = $detalleordenventa->actualizar($iddetalleordenventa, $datoOV);
                $dataRespuesta['verificacion'] = true;
            } else {
                $dataRespuesta['verificacion'] = false;
            }
        } else {
            $dataRespuesta['verificacion'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    /* Aprobaciones */

    function autorizarCreditos() {
        if (!$_REQUEST['idOrdenVenta']) {
            $orden = new OrdenVenta();
            $url = "/" . $_REQUEST['url'];
            $opciones = new general();
            $data['Opcion'] = $opciones->buscaOpcionexurl($url);
            $data['Modulo'] = $opciones->buscaModulosxurl($url);
            $data['ordenVenta'] = $orden->pedidoxaprobar(3);
            $data['documento'] = $this->tipoDocumento();
            $data['FormaPago'] = $this->formaPago();
            $data['CondicionLetra'] = $orden->condicionesletra();
            $data['TipoLetra'] = $this->tipoLetra();
            $this->view->show("creditos/aprobarpedido.phtml", $data);
        } elseif (!empty($_REQUEST['idOrdenVenta'])) {
            $id = $_REQUEST['idOrdenVenta'];
            $estadoOrden = $_REQUEST['estadoOrden'];
            $dataOrden = $_REQUEST['ordenVenta'];
            //$lacliente = $_REQUEST['lacliente'];
            $dataDetalleOrdenVenta = $_REQUEST['DetalleOrdenVenta'];
            $creditoDias = $_REQUEST['creditoDias'];
            $montoContado = $_REQUEST['montoContado'];
            $montoTotal = $dataOrden['importeordencobro'];
            /* Datos para crear el movimiento */
            $dm['idordenventa'] = $id;
            $dm['conceptomovimiento'] = 1;
            $dm['tipomovimiento'] = 2;
            $dm['idtipooperacion'] = 1;
            $dm['essunat'] = 1;
            $dm['fechamovimiento'] = date('Y/m/d');
            $ordenVenta = new OrdenVenta();
            $producto = new Producto();
            $movimiento = new Movimiento();
            $detalleMovimiento = new Detallemovimiento();
            $ordenCobro = new OrdenCobro();
            $detalleOrdenCobro = new detalleOrdenCobro();
            $condicionLetra = new CondicionLetra();
            $ordenGasto = $this->AutoLoadModel('ordengasto');
            $redondeo = $this->configIni('Globals', 'Redondeo');
            $montoPercepcion = 0;

            if ($dataOrden['idcondicionletra'] != '') {
                $dataCondicionLetra = $condicionLetra->buscaxId($dataOrden['idcondicionletra']);
            }
            /* if (lstDocumento==1){
              $dataOrden['importeordencobro']=round($dataOrden['importeordencobro']*1.02,$redondeo);
              $montoPercepcion=round($dataOrden['importeordencobro']*0.02,$redondeo);
              $dataGasto['importegasto']=$montoPercepcion;
              $dataGasto['idordenventa']=$id;
              $dataGasto['idtipogasto']=6;
              $grabaGasto=$ordenGasto->graba($dataGasto);


              } */

            /* Datos para la orden de cobro */
            $fechaActual = (!empty($_REQUEST['txtFechaCredito']) ? $_REQUEST['txtFechaCredito'] : date('Y/m/d'));
            $dataOrden['saldoordencobro'] = $dataOrden['importeordencobro'];
            $dataOrden['idordenventa'] = $id;
            $dataOrden['femision'] = $fechaActual;
            $dataOrden['numletras'] = $dataCondicionLetra[0]['cantidadletra'];
            /* Datos para actualizar la orden de venta */
            $dov['vbcreditos'] = $estadoOrden;

            if ($estadoOrden != 1) {
                $dov['desaprobado'] = 1;
                $dov['fdesaprobado'] = date('Y-m-d');
            }
            if ($estadoOrden == 1) {
                $dov['faprobado'] = date('Y-m-d');
                $dov['es_contado'] = $dataOrden['escontado'];
                $dov['es_credito'] = $dataOrden['escredito'];
                $dov['es_letras'] = $dataOrden['esletras'];
                $dov['tipo_letra'] = $dataOrden['tipoletra'];
            }

            $dov['importeov'] = $dataOrden['importeordencobro'];
            $dov['mcreditos'] = $_REQUEST['mensajeCreditos'];
            $dov['observaciones'] = $_REQUEST['observacion'];

            $exito1 = $ordenVenta->actualizaOrdenVenta($dov, $id);
            if ($estadoOrden == 1) {

                $exito2 = $movimiento->grabaMovimiento($dm);
                $exito3 = $ordenCobro->grabaOrdencobro($dataOrden);

                $dataGasto['idordenventa'] = $id;
                $dataGasto['importegasto'] = round($dataOrden['importeordencobro'] - $dataOrden['importeordencobro'] / 1.18, $redondeo);
                $dataGasto['idtipogasto'] = 7;
                $grabaGasto = $ordenGasto->graba($dataGasto);

                $dataGasto['idtipogasto'] = 9;
                $dataGasto['importegasto'] = round($dataOrden['importeordencobro'] / 1.18, $redondeo);
                $grabaGasto = $ordenGasto->graba($dataGasto);
                /* echo "    4  ";
                  if ($dataOrden['numletras'] > $this->configIni('LetraAdicional','cantidad')) {
                  $dataGasto['idtipogasto']=10;
                  $dataGasto['importegasto']=round($lacliente*$this->configIni('LetraAdicional','valor'),$redondeo);
                  $grabaGasto=$ordenGasto->graba($dataGasto);
                  } */
            }

            $productos = $_REQUEST['Producto'];
            $cantidad = count($productos);

            for ($i = 0; $i < $cantidad; $i++) {
                if ($estadoOrden == 2) {
                    for ($i = 0; $i < $cantidad; $i++) {
                        //buscamos producto
                        $idproducto = $productos[$i]['idproducto'];
                        $dataProducto = $producto->buscaProductoxId($idproducto);
                        $stockdisponibleA = $dataProducto[0]['stockdisponible'];
                        $stockdisponibleN = $stockdisponibleA + $productos[$i]['cantdespacho'];
                        $dataNuevo['stockdisponible'] = $stockdisponibleN;
                        //actualizamos es stockdisponible
                        $exitoP = $producto->actualizaProducto($dataNuevo, $idproducto);
                    }
                }
            }


            if ($exito1 and $exito2 and $exito3 and $estadoOrden == 1) {
                foreach ($dataDetalleOrdenVenta as $data) {



                    $stockNuevo = $data['stockactual'] - $data['cantidad'];
                    /* Datos para crear el detalle movimiento */
                    $ddm['idmovimiento'] = $exito2;
                    $idproducto = $ddm['idproducto'] = $data['idproducto'];
                    $ddm['pu'] = $data['pu'];
                    $pv = $producto->buscaProducto($idproducto);
                    $ddm['preciovalorizado'] = $pv[0]['preciocosto'];
                    $ddm['cantidad'] = $data['cantidad'];
                    $ddm['importe'] = $data['total'];
                    $ddm['stockdisponibledm'] = $stockNuevo;
                    $ddm['stockactual'] = $data['stockactual'] - $data['cantidad'];
                    $dataPro['stockactual'] = $stockNuevo;
                    if ($stockNuevo <= 0) {
                        $dataPro['esagotado'] = 1;
                        $dataPro['fechaagotado'] = date('Y-m-d');
                    }

                    $exito4 = $producto->actualizaProducto($dataPro, $data['idproducto']);
                    $exito5 = $detalleMovimiento->grabaDetalleMovimieto($ddm);
                }
                if ($exito4 and $exito5) {
                    //Disminuir el Saldo del Cliente:
                    $clienteposicion = New Cliente();
                    $idcliente = $clienteposicion->idclientexidordenventa($id);
                    $exito_cp = $clienteposicion->restarSaldo($idcliente, $dataOrden['importeordencobro']);
                    //
                    
                    //$ddoc = Data detalle orden cobro
                    $ddoc['idordencobro'] = $exito3;
                    $ddoc['fvencimiento'] = $fechaActual;
                    $mContado = ($_REQUEST['montoContado'] == '') ? 0 : ($_REQUEST['montoContado']);
                    $mCredito = ($_REQUEST['montoCredito'] == '') ? 0 : ($_REQUEST['montoCredito']);
                    $mLetras = ($_REQUEST['montoLetras'] == '') ? 0 : ($_REQUEST['montoLetras']);
                    $esContado = (!isset($dataOrden['escontado'])) ? 0 : 1;
                    $esCredito = (!isset($dataOrden['escredito'])) ? 0 : 1;
                    $esLetras = (!isset($dataOrden['esletras'])) ? 0 : 1;

                    if ($esContado) {
                        $mContado = ($esCredito + $esLetras > 0) ? $mContado : $dataOrden['importeordencobro'];
                        $exito6 = $this->grabaContado($mContado, $exito3, $fechaActual, $id);
                        $dataOrden['importeordencobro'] -= $mContado;
                    }
                    if ($esCredito) {
                        $mCredito = ($esLetras > 0) ? $mCredito : $dataOrden['importeordencobro'];
                        $exito7 = $this->grabaCredito($mCredito, $exito3, $fechaActual, $_REQUEST['creditoDias'], $id);
                    }
                    if ($esLetras) {
                        $mLetras = ($esCredito > 0) ? $mLetras : $dataOrden['importeordencobro'];
                        $dataCondicionLetra[0]['LaAcepCli'] = 0; //$lacliente;
                        $exito8 = $this->grabaLetra($mLetras, $exito3, $fechaActual, $dataCondicionLetra, $id, $montoPercepcion);
                    }
                }

                //graba el tiempor que demoro ser aprobado
                $ordenVentaDuracion = new ordenventaduracion();
                $DDA = $ordenVentaDuracion->listaOrdenVentaDuracion($id, "almacen");
                $dataDuracion['idordenventa'] = $id;
                $intervalo = $this->date_diff(date('Y-m-d H:i:s', strtotime($DDA[0]['fechacreacion'])), date('Y-m-d H:i:s'));
                $dataDuracion['tiempo'] = $intervalo;
                $dataDuracion['referencia'] = 'credito';
                $exitoN = $ordenVentaDuracion->grabaOrdenVentaDuracion($dataDuracion);


                $ruta['ruta'] = "/creditos/autorizarcreditos";
                $this->view->show("ruteador.phtml", $ruta);
            } elseif ($estadoOrden == 2) {
                $ruta['ruta'] = "/creditos/autorizarCreditos";
                $this->view->show("ruteador.phtml", $ruta);
            }
        } else {
            $ruta['ruta'] = "/creditos/autorizarCreditos";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function grabaContado($monto = '', $idoc, $fechaActual, $id) {
        $detalleOrdenCobro = new detalleOrdenCobro();
        $ordenVenta = $this->AutoLoadModel('ordenventa');
        if ($monto != '') {
            $data['idordencobro'] = $idoc;
            $data['fechagiro'] = $fechaActual;
            $data['fvencimiento'] = date('Y/m/d', strtotime($fechaActual));
            $data['importedoc'] = $monto;
            $data['saldodoc'] = $monto;
            $data['formacobro'] = 1;

            $od['fechavencimiento'] = $data['fvencimiento'];
            $grabando = $ordenVenta->actualizaOrdenVenta($od, $id);

            return $detalleOrdenCobro->grabaDetalleOrdenVentaCobro($data);
        }
    }

    function grabaCredito($monto = '', $idoc, $fechaActual, $creditoDias, $id) {
        $detalleOrdenCobro = new detalleOrdenCobro();
        $ordenVenta = $this->AutoLoadModel('ordenventa');
        if ($monto != '') {
            $data['idordencobro'] = $idoc;
            $data['fechagiro'] = $fechaActual;
            $data['importedoc'] = $monto;
            $data['saldodoc'] = $monto;
            $data['formacobro'] = 2;
            $data['fvencimiento'] = date('Y/m/d', strtotime("$fechaActual + " . $creditoDias . " day"));

            $od['fechavencimiento'] = $data['fvencimiento'];
            $grabando = $ordenVenta->actualizaOrdenVenta($od, $id);

            return $detalleOrdenCobro->grabaDetalleOrdenVentaCobro($data);
        }
    }

    function grabaLetra($monto = '', $idoc, $fechaActual, $dataCondicionLetra, $id, $montoPercepcion) {
        //GENERANDO LA LETRA:
        $documento = $this->AutoLoadModel('documento');
        $ordenVenta = $this->AutoLoadModel('ordenventa');
        $ordenCobro = new OrdenCobro();
        $detalleOrdenCobro = new detalleOrdenCobro();
        if ($monto != '') {
            $ddoc['idordencobro'] = $idoc;
            $diasLetra = split('/', $dataCondicionLetra[0]['nombreletra']);
            $numeroletra = $dataCondicionLetra[0]['cantidadletra'];
            $exito = false;
            $totalAdicionalLetra = 0;
            if ($numeroletra > 3)
                $totalAdicionalLetra = $dataCondicionLetra[0]['LaAcepCli'] * $this->configIni('LetraAdicional', 'valor');;
            for ($i = 0; $i < $numeroletra; $i++) {
                $nrodias = 0; //(($detalleOrdenCobro->fechagironrodias($idoc))==1?2:5);
                $diasLetra[$i] += $nrodias;
                $ddoc['fechagiro'] = date('Y/m/d', strtotime("$fechaActual + " . $nrodias . " day"));
                $ddoc['formacobro'] = 3;
                $monto = $monto - $montoPercepcion;
                if ($i == 0) {
                    $ddoc['importedoc'] = round($monto / $numeroletra, 2) + $montoPercepcion + $totalAdicionalLetra;
                    $ddoc['saldodoc'] = round($monto / $numeroletra, 2) + $montoPercepcion + $totalAdicionalLetra;
                } else {
                    $ddoc['importedoc'] = round($monto / $numeroletra, 2);
                    $ddoc['saldodoc'] = round($monto / $numeroletra, 2);
                }
                $ddoc['numeroletra'] = $detalleOrdenCobro->GeneraNumeroLetra();
                $ddoc['fvencimiento'] = date('Y/m/d', strtotime("$fechaActual + " . $diasLetra[$i] . " day"));
                $exito = $detalleOrdenCobro->grabaDetalleOrdenVentaCobro($ddoc);
                //Generando los documentos de las letras
                $datadocumentos['idordenventa'] = $id;
                $datadocumentos['fechadoc'] = $ddoc['fechagiro'];
                $datadocumentos['numdoc'] = $ddoc['numeroletra'];
                $datadocumentos['serie'] = 1;
                $datadocumentos['montofacturado'] = $ddoc['importedoc'];
                $datadocumentos['nombredoc'] = 7;
                $graba = $documento->grabaDocumento($datadocumentos);
                if (!$exito || !$graba)
                    break;

                if ($i == $numeroletra - 1) {
                    $od['fechavencimiento'] = $ddoc['fvencimiento'];
                    $grabando = $ordenVenta->actualizaOrdenVenta($od, $id);
                }
            }
            if ($numeroletra > 3) {
                $datOC['importeordencobro'] = $monto + $totalAdicionalLetra;
                $datOC['saldoordencobro'] = $monto + $totalAdicionalLetra;
                $ordenCobro->actualizaOrdencobro($datOC, $idoc);
            }
            return $exito;
        }
    }

     function listadoEvaluacionCrediticia() {
        set_time_limit(300);
        $cliente = $this->AutoLoadModel('cliente');
        $tipocambio=$this->AutoLoadModel('tipocambio');
        $creditos = $this->AutoLoadModel('creditos');
        $idpadrec = $_REQUEST['lstCategoriaPrincipal'];
        $idcategoria = $_REQUEST['lstZonaCobranza'];
        $idzona = $_REQUEST['lstZona'];
        $idcliente = $_REQUEST['idCliente'];
        $idvendedor= $_REQUEST['idVendedor'];
        $orden1= $_REQUEST['orden1'];
        $filtro1= $_REQUEST['filtro1'];
        $filtro2= $_REQUEST['filtro2'];
        $filtro3= $_REQUEST['filtro3'];
        $updateresumen= $_REQUEST['updateresumen'];
        $in=$_REQUEST['txtIn'];

        $listaCalificaciones=$cliente->listaCalificaciones();
        $listaCondicionCompra=$cliente->listaCondicionCompra();

        $consultavigentehoy=$tipocambio->consultavigentehoy();
        $get_tcambio=$consultavigentehoy[0]['compra'];

        if($idvendedor==''){
            $data1=$cliente->listaClientesZonaparaCobranza($idzona,$idpadrec,$idcategoria,$idcliente,$orden1);
            if($idpadrec=="" and $idcategoria=="" and $idzona=="" and $idcliente!=""){
                if(count($data1)>1){ $data1[0]=null; }
            }
        }
        if($idvendedor!=''){
            $data1=$cliente->listaClientesZonaparaCobranzaVendedor($idvendedor);
            if($idpadrec=="" and $idcategoria=="" and $idzona=="" and $idcliente!=""){
                if(count($data1)>1){ $data1[0]=null; }
            }
        }
        if($updateresumen==1){
            $data1=$cliente->listadoclientes_evaluacioncrediticia($in);
            if($idpadrec=="" and $idcategoria=="" and $idzona=="" and $idcliente!=""){
                if(count($data1)>1){ $data1[0]=null; }

            }
        }


        for($i=0;$i<count($data1);$i++){
            if($data1[$i]!=null){
                $lista_deuda_contado='';
                $lista_deuda_credito='';
                $lista_deuda_letrabanco='';
                $lista_deuda_letracartera='';
                $lista_deuda_letraprotestada='';

                $tempVendidoSoles=0.00;
                $tempPagadoSoles=0.00;
                $tempDeudaSoles=0.00;
                $tempDeudaContadoSoles=0.00;
                $tempDeudaCreditoSoles=0.00;
                $tempDeudaletrabancoSoles=0.00;
                $tempDeudaletracarteraSoles=0.00;
                $tempDeudaletraprotestadaSoles=0.00;

                $tempVendidoDolares=0.00;
                $tempPagadoDolares=0.00;
                $tempDeudaDolares=0.00;
                $tempDeudaContadoDolares=0.00;
                $tempDeudaCreditoDolares=0.00;
                $tempDeudaletrabancoDolares=0.00;
                $tempDeudaletracarteraDolares=0.00;
                $tempDeudaletraprotestadaDolares=0.00;

                $tempDeudaTotal=0.00;

               $lista_deuda_contado=$cliente->listaDeudaTotalCliente($data1[$i]['idcliente'],'contado');
               $lista_deuda_credito=$cliente->listaDeudaTotalCliente($data1[$i]['idcliente'],'credito');
               $lista_deuda_letrabanco=$cliente->listaDeudaTotalCliente($data1[$i]['idcliente'],'letrabanco');
               $lista_deuda_letracartera=$cliente->listaDeudaTotalCliente($data1[$i]['idcliente'],'letracartera');
               $lista_deuda_letraprotestada=$cliente->listaDeudaTotalCliente($data1[$i]['idcliente'],'letraprotestada');
               $ultimoPagoCliente=$cliente->ultimoPagoCliente($data1[$i]['idcliente']);
               $listaCalificacionActual=$cliente->listaCalificacionActual($data1[$i]['idcliente']);
               $listaCondicionCompraActual=$cliente->listaCondicionCompraActual($data1[$i]['idcliente']);
               $ultimaCompraCliente=$cliente->ultimaCompraCliente($data1[$i]['idcliente']);

               foreach ($lista_deuda_contado as $value) {
                   if($value['idmoneda']==1){
                    $tempVendidoSoles=$tempVendidoSoles+($value['importedoc']);
                    $tempPagadoSoles=$tempPagadoSoles+($value['importedoc']-$value['saldodoc']);
                    $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                    $tempDeudaContadoSoles=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
                   if($value['idmoneda']==2){
                    $tempVendidoDolares=$tempVendidoDolares+($value['importedoc']);
                    $tempPagadoDolares=$tempPagadoDolares+($value['importedoc']-$value['saldodoc']);
                    $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                    $tempDeudaContadoDolares=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
               }

               foreach ($lista_deuda_credito as $value) {
                   if($value['idmoneda']==1){
                    $tempVendidoSoles=$tempVendidoSoles+($value['importedoc']);
                    $tempPagadoSoles=$tempPagadoSoles+($value['importedoc']-$value['saldodoc']);
                    $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                    $tempDeudaCreditoSoles=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
                   if($value['idmoneda']==2){
                    $tempVendidoDolares=$tempVendidoDolares+($value['importedoc']);
                    $tempPagadoDolares=$tempPagadoDolares+($value['importedoc']-$value['saldodoc']);
                    $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                    $tempDeudaCreditoDolares=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
               }

               foreach ($lista_deuda_letrabanco as $value) {
                   if($value['idmoneda']==1){
                    $tempVendidoSoles=$tempVendidoSoles+($value['importedoc']);
                    $tempPagadoSoles=$tempPagadoSoles+($value['importedoc']-$value['saldodoc']);
                    $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                    $tempDeudaletrabancoSoles=($value['importedoc']-($value['importedoc']-$value['saldodoc']));

                   }
                   if($value['idmoneda']==2){
                    $tempVendidoDolares=$tempVendidoDolares+($value['importedoc']);
                    $tempPagadoDolares=$tempPagadoDolares+($value['importedoc']-$value['saldodoc']);
                    $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                    $tempDeudaletrabancoDolares=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
               }

               foreach ($lista_deuda_letracartera as $value) {
                   if($value['idmoneda']==1){
                    $tempVendidoSoles=$tempVendidoSoles+($value['importedoc']);
                    $tempPagadoSoles=$tempPagadoSoles+($value['importedoc']-$value['saldodoc']);
                    $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                    $tempDeudaletracarteraSoles=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
                   if($value['idmoneda']==2){
                    $tempVendidoDolares=$tempVendidoDolares+($value['importedoc']);
                    $tempPagadoDolares=$tempPagadoDolares+($value['importedoc']-$value['saldodoc']);
                    $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                    $tempDeudaletracarteraDolares=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
               }

               foreach ($lista_deuda_letraprotestada as $value) {
                   if($value['idmoneda']==1){
                    $tempVendidoSoles=$tempVendidoSoles+($value['importedoc']);
                    $tempPagadoSoles=$tempPagadoSoles+($value['importedoc']-$value['saldodoc']);
                    $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                    $tempDeudaletraprotestadaSoles=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
                   if($value['idmoneda']==2){
                    $tempVendidoDolares=$tempVendidoDolares+($value['importedoc']);
                    $tempPagadoDolares=$tempPagadoDolares+($value['importedoc']-$value['saldodoc']);
                    $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                    $tempDeudaletraprotestadaDolares=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
               }
                $tempDeudaTotal=$tempDeudaSoles+($tempDeudaDolares*$get_tcambio);
                $calcularCreditoDisponible=$cliente->calcularCreditoDisponible($data1[$i]['idcliente'],$tempDeudaTotal,$get_tcambio,'');
                $lineaCreditoDisponible=$calcularCreditoDisponible[0]['lineacreditoactual']-$calcularCreditoDisponible[0]['deudatotal'];
                $clienteAuditado=$creditos->clienteAuditado($data1[$i]['idcliente']);

                $dataFinal[]=array("d1_idcliente"=>$data1[$i]['idcliente'],
                                 "d1_iddistrito"=>$data1[$i]['iddistrito'],
                                 "d1_razonsocial"=>$data1[$i]['razonsocial'],
                                 "d1_direccion"=>$data1[$i]['direccion'],
                                 "d1_idcategoria"=>$data1[$i]['idcategoria'],
                                 "d1_idpadrerec"=>$data1[$i]['idpadrerec'],
                                 "d1_codigoc"=>$data1[$i]['codigoc'],
                                 "d1_nombrec"=>$data1[$i]['nombrec'],
                                 "d1_idzona"=>$data1[$i]['idzona'],
                                 "d1_nombrezona"=>$data1[$i]['nombrezona'],
                                 "d1_fechacreacion"=>$data1[$i]['fechacreacion'],
                                 "totalvendidosoles"=>$tempVendidoSoles,
                                 "totalvendidodolares"=>$tempVendidoDolares,
                                 "totalpagadosoles"=>$tempPagadoSoles,
                                 "totalpagadodolares"=>$tempPagadoDolares,
                                 "totaldeudasoles"=>$tempDeudaSoles,
                                 "totaldeudadolares"=>$tempDeudaDolares,
                                 "deudacontadosoles"=>$tempDeudaContadoSoles,
                                 "deudacreditosoles"=>$tempDeudaCreditoSoles,
                                 "deudaletrabancosoles"=>$tempDeudaletrabancoSoles,
                                 "deudaletracarterasoles"=>$tempDeudaletracarteraSoles,
                                 "deudaletraprotestadasoles"=>$tempDeudaletraprotestadaSoles,
                                 "deudacontadodolares"=>$tempDeudaContadoDolares,
                                 "deudacreditodolares"=>$tempDeudaCreditoDolares,
                                 "deudaletrabancodolares"=>$tempDeudaletrabancoDolares,
                                 "deudaletracarteradolares"=>$tempDeudaletracarteraDolares,
                                 "deudaletraprotestadadolares"=>$tempDeudaletraprotestadaDolares,
                                 "fcobro"=>$ultimoPagoCliente[0]['fcobro'],
                                 "idordenventa"=>$ultimoPagoCliente[0]['idordenventa'],
                                 "codigov"=>$ultimoPagoCliente[0]['codigov'],
                                 "montoasignado"=>$ultimoPagoCliente[0]['montoasignado'],
                                 "idmoneda"=>$ultimoPagoCliente[0]['idmoneda'],
                                 "lineacreditoactual"=>$calcularCreditoDisponible[0]['lineacreditoactual'],
                                 "deudatotal"=>$calcularCreditoDisponible[0]['deudatotal'],
                                 "lineacreditodisponible"=>$lineaCreditoDisponible,
                                 "prueba"=>$tempDeudaTotal,
                                 "idcalificacion"=>$listaCalificacionActual[0]['idcalificacion'],
                                 "calificacion"=>$listaCalificacionActual[0]['calificacion'],
                                 "auditado"=>$clienteAuditado[0]['auditado'],
                                 "idcondicioncompra"=>$listaCondicionCompraActual[0]['idcondicioncompra'],
                                 "condicioncompra"=>$listaCondicionCompraActual[0]['condicioncompra'],
                                 "ov_codigov"=>$ultimaCompraCliente[0]['codigov'],
                                 "ov_fordenventa"=>$ultimaCompraCliente[0]['fordenventa'],
                                 "ov_importeordenventa"=>$ultimaCompraCliente[0]['importeordenventa'],
                                 "ov_percepcion"=>$ultimaCompraCliente[0]['percepcion'],
                                 "ov_gastosadicionales"=>$ultimaCompraCliente[0]['gastosadicionales'],
                                 "ov_idmoneda"=>$ultimaCompraCliente[0]['idmoneda'],
                                 );
            }
        }
        if($filtro1!=""){
            for($i=0;$i<count($dataFinal);$i++){
                if($dataFinal[$i]!=null){
                    if($filtro1=="1"){ //con deuda
                        if($dataFinal[$i]['deudatotal']>0){
                        }else{  $dataFinal[$i]=null; }
                    }
                    if($filtro1=="2"){ //sin deuda
                        if($dataFinal[$i]['deudatotal']==0){
                        }else{  $dataFinal[$i]=null; }
                    }
                    if($filtro1=="3"){ //con linea de credito disponible
                        if($dataFinal[$i]['lineacreditodisponible']>0){
                        }else{  $dataFinal[$i]=null; }
                    }
                    if($filtro1=="4"){ //sin linea de credito disponible
                        if($dataFinal[$i]['lineacreditodisponible']<=0){
                        }else{  $dataFinal[$i]=null; }
                    }
                }
            }
        }

        if($filtro2!=""){
            for($i=0;$i<count($dataFinal);$i++){
                if($dataFinal[$i]!=null){
                    if($filtro2=="1"){ //auditados
                        if($dataFinal[$i]['auditado']=="1"){
                        }else{  $dataFinal[$i]=null; }
                    }
                    if($filtro2=="2"){ //no auditados
                        if($dataFinal[$i]['auditado']=="0"){
                        }else{  $dataFinal[$i]=null; }
                    }
                }
            }
        }
        if($filtro3!=""){
            for($i=0;$i<count($dataFinal);$i++){
                if($dataFinal[$i]!=null){
                    if($filtro3==$dataFinal[$i]['idcalificacion']){ //Busqueda por Calificacion
                    }else{  $dataFinal[$i]=null; }
                }
            }
        }

        //echo json_encode($dataFinal);
        $temp_idcliente=-1;
        for($i=0;$i<count($dataFinal);$i++){
            $name_condicioncompra='';
            $name_calificacioncompra='';
            if($dataFinal[$i]!=null){
                if($dataFinal[$i]['d1_idcliente']!=$temp_idcliente){
                    $moneda='';
                    $ultimacompra_moneda='';
                    if($dataFinal[$i]['ov_idmoneda']==1){ $ultimacompra_moneda='S/.&nbsp;'; }
                    if($dataFinal[$i]['ov_idmoneda']==2){ $ultimacompra_moneda='US $.'; }
                    $fila .= "<table id='table".$i."'>";
                    $fila .= "<tr>";
                    $fila .= "<th>Fecha&nbsp;Ultima&nbsp;Compra</th>";
                    $fila .= "<td style='font-size:12px;'>".$dataFinal[$i]['ov_fordenventa']."</td>";
                    $fila .= "<th>Ultima&nbsp;Compra</th>";
                    $fila .= "<td style='font-size:12px;'>".$dataFinal[$i]['ov_codigov']."</td>";
                    $fila .= "<th>Importe Ult Compra</th>";
                    $fila .= "<td>".$ultimacompra_moneda.' '.$dataFinal[$i]['ov_importeordenventa']."</td>";
                    $fila .= "<td colspan='2'style='background-color:#E8F1FC;font-weight:600;'>Percepcion Ult Compra&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$ultimacompra_moneda.' '.$dataFinal[$i]['ov_percepcion']."</td>";
                    $fila .= "<td style='background-color:#E8F1FC;font-weight:600;'>Gastos&nbsp;Adicionales</td>";
                    $fila .= "<td>".$ultimacompra_moneda.' '.$dataFinal[$i]['ov_gastosadicionales']."</td>";
                    $fila .= "</tr>";
                    $fila .= "<tr>";
                    $fila .= "<th>Zona Cobranza</th>";
                    $fila .= "<th>Zona Detalle</th>";
                    $fila .= "<th>Cliente</th>";
                    $fila .= "<th>Ultimo Pago</th>";
                    $fila .= "<th>Fecha Ultimo Pago</th>";
                    $fila .= "<th style='font-size:12px !important;'>Total&nbsp;Comprado&nbsp;S/.</th>";
                    $fila .= "<th style='font-size:12px !important;'>Total&nbsp;Comprado&nbsp;$.</th>";
                    $fila .= "<th style='font-size:12px !important;'>Total&nbsp;Pagado&nbsp;S/.</th>";
                    $fila .= "<th style='font-size:12px !important;'>Total&nbsp;Pagado&nbsp;$.</th>";
                    $fila .= "<th>CALIFICACION </th>";
                    $fila .= "</tr>";

                    $fila .= "<tr style='text-align:center !important;background-color:white;'>";
                    $fila .= "<td class='centro2' style='font-size:12px !important;'>".$dataFinal[$i]['d1_nombrec']."</td>";
                    $fila .= "<td class='centro2' style='font-size:12px !important;'>".$dataFinal[$i]['d1_nombrezona']."</td>";
                    $fila .= "<td class='centro2' style='font-size:11px !important;'>".substr($dataFinal[$i]['d1_razonsocial'], 0, 25)."</td>";
                    if($dataFinal[$i]['montoasignado']!=''){
                        if($dataFinal[$i]['idmoneda']==1){ $moneda='S/.&nbsp;'; }
                        if($dataFinal[$i]['idmoneda']==2){ $moneda='US $.'; }
                        $fila .= "<td class='centro2' style='font-size:12px'>".$moneda.' '.number_format(round($dataFinal[$i]['montoasignado'],2), 2, ".", "").' => '.$dataFinal[$i]['codigov']."</td>";
                        $fila .= "<td class='centro2'>".$dataFinal[$i]['fcobro']."</td>";
                    }else{
                        $fila .= "<td class='centro2'>&nbsp;</td>";
                        $fila .= "<td class='centro2'>&nbsp;</td>";
                    }
                    $fila .= "<td class='centro2'>".'S/.&nbsp;'.number_format(round($dataFinal[$i]['totalvendidosoles'],2), 2, ".", "")."</td>";
                    $fila .= "<td class='centro2'>".'$.&nbsp;'.number_format(round($dataFinal[$i]['totalvendidodolares'],2), 2, ".", "")."</td>";
                    $fila .= "<td class='centro2'>".'S/.&nbsp;'.number_format(round($dataFinal[$i]['totalpagadosoles'],2), 2, ".", "")."</td>";
                    $fila .= "<td class='centro2'>".'$.&nbsp;'.number_format(round($dataFinal[$i]['totalpagadodolares'],2), 2, ".", "")."</td>";
                    $fila .= "<td  class='centro2'>";
                    $fila .= "<select id='cmbCalificacion".$i."' name='cmbCalificacion".$i."' style='width:170px;font-size:13px'>";
                    if($dataFinal[$i]['idcalificacion']!=''){
                                $name_calificacioncompra=$dataFinal[$i]['calificacion'];
                                $fila .= "<option value='".$dataFinal[$i]['idcalificacion']."'>".$dataFinal[$i]['calificacion']."</option>";
                            foreach ($listaCalificaciones as $v) {
                                if($v['idcalificacion']!=$dataFinal[$i]['idcalificacion']){
                                    $fila .= "<option value='".$v['idcalificacion']."'>".$v['nombre']."</option>";
                                }
                            }
                    }else{
                        $fila .= "<option value=''>--seleccione--</option>";
                        foreach ($listaCalificaciones as $v) {
                                $fila .= "<option value='".$v['idcalificacion']."'>".$v['nombre']."</option>";
                        }
                    }
                    $fila .= "</select></td>";
                    $fila .= "<td><span style='cursor:pointer;' class='spanGrabar'><img width='25' height='25' src='/imagenes/grabar.gif' class='btnGrabar' value='".$i."'></span></td>";
                    $fila .= "</tr>";

                    $fila.="<tr>";
                    $fila .= "<th>Deuda Contado</th>";
                    $fila .= "<th>Deuda Credito</th>";
                    $fila .= "<th>Deuda Letra Banco</th>";
                    $fila .= "<th>CONDICION COMPRA</th>";
                    $fila .= "<th  style='display:none !important;'>Letra Cartera</th>";
                    $fila .= "<th>Deuda Letra protestada</th>";
                    $fila .= "<th>Linea credito</th>";
                    $fila .= "<th>Deuda Total</th>";
                    $fila .= "<th>Lin credito disponible</th>";
                    $fila .= "<th>Modificar Linea Credito</th>";
                    $fila .= "<th >Observacion</th>";
                    $fila .= "</tr>";

                    $fila .= "<tr  style='text-align:center !important; background-color: white !important;'>";
                    $fila .= "<td class='centro1'><input id='txtdcontado_s".$i."'  disabled type='text' class='cajitasIzq' value='S/.".number_format(round($dataFinal[$i]['deudacontadosoles'],2), 2, ".", "")."'/><input  id='txtdcontado_d".$i."'   disabled type='text' class='cajitasDer'  value='$. ".number_format(round($dataFinal[$i]['deudacontadodolares'],2), 2, ".", "")."'/></td>";
                    $fila .= "<td class='centro1'><input id='txtdcredito_s".$i."'  disabled type='text' class='cajitasIzq' value='S/.".number_format(round($dataFinal[$i]['deudacreditosoles'],2), 2, ".", "")."'/><input  id='txtdcredito_d".$i."'  disabled type='text' class='cajitasDer'  value='$. ".number_format(round($dataFinal[$i]['deudacreditodolares'],2), 2, ".", "")."'/></td>";
                    $fila .= "<td class='centro1'><input id='txtdletrabanco_s".$i."'  disabled type='text' class='cajitasIzq' value='S/.".number_format(round($dataFinal[$i]['deudaletrabancosoles'],2), 2, ".", "")."'/><input  id='txtdletrabanco_d".$i."' disabled type='text' class='cajitasDer'  value='$. ".number_format(round($dataFinal[$i]['deudaletrabancodolares'],2), 2, ".", "")."'/></td>";
                    $fila .= "<td  style='display:none !important;' class='centro1'><input id='txtdletracartera_s".$i."'  disabled type='text' class='cajitasIzqCartera' value='S/.".number_format(round($dataFinal[$i]['deudaletracarterasoles'],2), 2, ".", "")."'/><input  id='txtdletracartera_d".$i."'  disabled type='text' class='cajitasDerCartera'  value='$. ".number_format(round($dataFinal[$i]['deudaletracarteradolares'],2), 2, ".", "")."'/></td>";

                    $fila .= "<td class='centro1'>";
                    $fila .= "<select id='cmbCondicionCompra".$i."' name='cmbCondicionCompra".$i."' style='width:125px;font-size:10px'>";
                    if($dataFinal[$i]['idcondicioncompra']!=''){
                        $name_condicioncompra=$dataFinal[$i]['condicioncompra'];
                                $fila .= "<option value='".$dataFinal[$i]['idcondicioncompra']."'>".$dataFinal[$i]['condicioncompra']."</option>";
                            foreach ($listaCondicionCompra as $v) {
                                if($v['idcondicioncompra']!=$dataFinal[$i]['idcondicioncompra']){
                                    $fila .= "<option value='".$v['idcondicioncompra']."'>".$v['nombre']."</option>";
                                }
                            }
                    }else{
                        $fila .= "<option value=''>--seleccione--</option>";
                        foreach ($listaCondicionCompra as $v) {
                                $fila .= "<option value='".$v['idcondicioncompra']."'>".$v['nombre']."</option>";
                        }
                    }
                    $fila .= "</select></td>";

                    $fila .= "<td class='centro1'><input id='txtdletraprotestada_s".$i."'  disabled type='text' class='cajitasIzq' value='S/.".number_format(round($dataFinal[$i]['deudaletraprotestadasoles'],2), 2, ".", "")."'/><input  id='txtdletraprotestada_d".$i."'  disabled type='text' class='cajitasDer'  value='$. ".number_format(round($dataFinal[$i]['deudaletraprotestadadolares'],2), 2, ".", "")."'/></td>";
                    $fila .= "<td class='centro1'><input id='txtLineaCredito".$i."'  style='width:90px !important;' disabled type='text' class='cajitasIzq letraresaltado'  value='S/.".number_format(round($dataFinal[$i]['lineacreditoactual'],2), 2, ".", "")."'/></td>";
                    $fila .= "<td class='centro1'><input id='txtDeudaTotal".$i."'  style='width:90px !important;' disabled type='text' class='cajitasIzq letraresaltado' value='S/.".number_format(round($dataFinal[$i]['deudatotal'],2), 2, ".", "")."'/></td>";
                    $fila .= "<td class='centro1'><input id='txtLineaCreditoDisponible".$i."' style='width:100px !important;'  disabled type='text' class='cajitasIzq letraresaltado' value='S/.".number_format(round($dataFinal[$i]['lineacreditodisponible'],2), 2, ".", "")."'/></td>";
                    $fila .= "<td class='letraResaltado centro1'>";
                    $fila .= "<select id='cmbMovimiento".$i."' style='font-size:11px;' ><option value='1'>aumentar</option><option value='2'>disminuir</option></select>";
                    $fila .= "<input type='text' id='txtCantidad".$i."' size=3 placeholder='$. 0.00'/>";
                    $fila .= "</td>";
                    $fila .= "<td class='letraResaltado centro1'><input type='text' id='txtObservacion".$i."' value=''/></td>";
                    $fila .= "<td><span id='btnVer".$i."' value='".'table'.$i.'|'.'tr'.$i.'|'.$dataFinal[$i]['d1_idcliente']."' class='desplegar' style='cursor:pointer;'><img width='17' heigth='17' src='/imagenes/ojo1.png' /></span></td>";
                    $fila .= "</tr>";
                    $fila.="<tr id='tr".$i."' value='0' rowspan=3 style='color: #80808029; background-color: #80808029;'><td colspan=10>&nbsp;<br><br><br></td></tr></table>";
                    }

                    if($updateresumen==1){
                        $insert_update_resumenevaluacioncrediticia=$creditos->insert_update_resumenevaluacioncrediticia(
                                $dataFinal[$i]['d1_idcliente'],
                                number_format(round($dataFinal[$i]['deudacontadosoles'],2), 2, ".", ""),
                                number_format(round($dataFinal[$i]['deudacontadodolares'],2), 2, ".", ""),
                                number_format(round($dataFinal[$i]['deudacreditosoles'],2), 2, ".", ""),
                                number_format(round($dataFinal[$i]['deudacreditodolares'],2), 2, ".", ""),
                                number_format(round($dataFinal[$i]['deudaletrabancosoles'],2), 2, ".", ""),
                                number_format(round($dataFinal[$i]['deudaletrabancodolares'],2), 2, ".", ""),
                                number_format(round($dataFinal[$i]['deudaletraprotestadasoles'],2), 2, ".", ""),
                                number_format(round($dataFinal[$i]['deudaletraprotestadadolares'],2), 2, ".", ""),
                                number_format(round($dataFinal[$i]['lineacreditoactual'],2), 2, ".", ""),
                                number_format(round($dataFinal[$i]['deudatotal'],2), 2, ".", ""),
                                number_format(round($dataFinal[$i]['lineacreditodisponible'],2), 2, ".", ""),
                                $dataFinal[$i]['ov_fordenventa'],
                                $dataFinal[$i]['ov_codigov'],
                                str_replace ('&nbsp;' , '' ,$ultimacompra_moneda) .$dataFinal[$i]['ov_importeordenventa'],
                                $dataFinal[$i]['fcobro'],
                                $dataFinal[$i]['codigov'],
                                str_replace ('&nbsp;' , '' ,$moneda) .number_format(round($dataFinal[$i]['montoasignado'],2), 2, ".", ""),
                                $name_condicioncompra,
                                $name_calificacioncompra,
                                1);
                    }
                $temp_idcliente=$dataFinal[$i]['d1_idcliente'];
            }
        }
        echo $fila;
    }

    function historialcredito2($idcliente=''){
        $creditos = $this->AutoLoadModel('creditos');
        $idcliente=$_REQUEST['idcliente'];
        $historialcredito=$creditos->historialcredito2($idcliente,'');
        if(count($historialcredito)==0){
            $historialcredito=array("resultado"=>"noauditado");
        }
        echo json_encode($historialcredito);
    }
    function  grabarLineaCredito(){
        $deudaactual_es_creditodisponible="1";

        $creditos = $this->AutoLoadModel('creditos');
        $idcliente=$_REQUEST['idcliente'];
        $dcontado_s=$_REQUEST['txtdcontado_s'];
        $dcontado_d=$_REQUEST['txtdcontado_d'];
        $dcredito_s=$_REQUEST['txtdcredito_s'];
        $dcredito_d=$_REQUEST['txtdcredito_d'];
        $dletrabanco_s=$_REQUEST['txtdletrabanco_s'];
        $dletrabanco_d=$_REQUEST['txtdletrabanco_d'];
        $dletracartera_s=$_REQUEST['txtdletracartera_s'];
        $dletracartera_d=$_REQUEST['txtdletracartera_d'];
        $dletraprotestada_s=$_REQUEST['txtdletraprotestada_s'];
        $dletraprotestada_d=$_REQUEST['txtdletraprotestada_d'];
        $nuevo_txtLineaCredito_soles=$_REQUEST['txtLineaCredito_soles'];

        $deudasoles=$dcontado_s+$dcredito_s+$dletrabanco_s+$dletracartera_s+$dletraprotestada_s;
        $deudadolares=$dcontado_d+$dcredito_d+$dletrabanco_d+$dletracartera_d+$dletraprotestada_d;

        $historialcredito=$creditos->historialcredito($idcliente,'filaultima');
//        if($deudaactual_es_creditodisponible=="0"){
//            $lcreditosoles=$historialcredito[0]['lcreditosoles'];
//            if($historialcredito[0]['movimiento']==1){
//                $lcreditodolares=$historialcredito[0]['lcreditodolares']+$historialcredito[0]['cantidad'];
//            }
//            if($historialcredito[0]['movimiento']==2){
//                $lcreditodolares=$historialcredito[0]['lcreditodolares']-$historialcredito[0]['cantidad'];
//            }
//        }
        if($deudaactual_es_creditodisponible=="1"){
            if(count($historialcredito)>0){
                $lcreditosoles=$historialcredito[0]['lcreditosoles'];
                if($historialcredito[0]['movimiento']==1){
                    $lcreditodolares=$historialcredito[0]['lcreditodolares']+$historialcredito[0]['cantidad'];
                }
                if($historialcredito[0]['movimiento']==2){
                    $lcreditodolares=$historialcredito[0]['lcreditodolares']-$historialcredito[0]['cantidad'];
                }
            }
            if(count($historialcredito)==0){
                if(($deudasoles+$deudadolares)>0){
                    $lcreditosoles=$deudasoles;
                    $lcreditodolares=$deudadolares;
                }
                if(($deudasoles+$deudadolares)<=0){
                    $lcreditosoles=$nuevo_txtLineaCredito_soles;
                    $lcreditodolares=0;
                }
                if($_REQUEST['cmbMovimiento']==1){
                    $lcreditodolares=$lcreditodolares+$historialcredito[0]['cantidad'];
                }
                if($_REQUEST['cmbMovimiento']==2){
                    $lcreditodolares=$lcreditodolares-$historialcredito[0]['cantidad'];
                }
            }
        }
        $desactivarCreditoDisponibleVigente=$creditos->desactivarCreditoDisponibleVigente($idcliente);

        $movimiento=$_REQUEST['cmbMovimiento'];
        $condicioncompra=$_REQUEST['cmbCondicionCompra'];
        $cantidad=$_REQUEST['txtCantidad'];
        $idcalificacion=$_REQUEST['cmbCalificacion'];
        $condcompra=0;
        $observaciones=$_REQUEST['txtObservacion'];
        $anulado=0;
        $estado=1;
        $tcambio=0.00;
        $usuariocreacion=$_SESSION['idactor'];
        $fechacreacion=date("Y-m-d H:i:s");

        $grabarClienteObservaciones=$creditos->grabarClienteObservaciones($idcliente,$condicioncompra,$idcalificacion,$observaciones,'credito');
        $agregarLineaCredito=$creditos->agregarLineaCredito($idcliente,$lcreditosoles,$lcreditodolares,$deudasoles,$deudadolares,$movimiento,$cantidad,$idcalificacion,$condcompra,$observaciones,$anulado,$estado,$dcontado_s,$dcontado_d,$dcredito_s,$dcredito_d,$dletrabanco_s,$dletrabanco_d,$dletracartera_s,$dletracartera_d,$dletraprotestada_s,$dletraprotestada_d,$tcambio,$usuariocreacion,$fechacreacion,$condicioncompra);
        if($agregarLineaCredito){ $resultado=1; }else{ $resultado=0; }
        echo json_encode(array("resultado"=>$resultado));
    }
    function  calcularCreditoDisponible(){
        $cliente = $this->AutoLoadModel('cliente');
        $tipocambio=$this->AutoLoadModel('tipocambio');
        $consultavigentehoy=$tipocambio->consultavigentehoy();
        $get_tcambio=$consultavigentehoy[0]['compra'];
        $calcularCreditoDisponible=$cliente->calcularCreditoDisponible($_REQUEST['idcliente'],'',$get_tcambio,'calcular');
        echo json_encode($calcularCreditoDisponible);
    }
    function  listaCondicionCompraActual(){
        $cliente = $this->AutoLoadModel('cliente');
        $listaCondicionCompraActual=$cliente->listaCondicionCompraActual($_REQUEST['idcliente']);
        if(count($listaCondicionCompraActual)==0){
            $listaCondicionCompraActual=array("resultado"=>"noauditado");
        }
        echo json_encode($listaCondicionCompraActual);
    }

    function generarcodigoverificacion() {
        $actorrol=new actorrol();
        $dataOpciones=New Opciones();
        $motivoReprogramado = $this->configIniTodo('MotivoReprogramacion');
        if (!empty($_REQUEST['txtDescripcion']) && $_REQUEST['txtMotivo'] > 0 && $_REQUEST['txtIdordenventa'] > 0 && $_REQUEST['txtUsuario'] > 0 && $_REQUEST['txtModulo'] > 0) {
            $idordenventa = $_REQUEST['txtIdordenventa'];
            $idactor = $_REQUEST['txtUsuario'];
            $idmodulo = $_REQUEST['txtModulo'];
            $idmotivo = $_REQUEST['txtMotivo'];
            $txtdescripcion = $_REQUEST['txtDescripcion'];
            if (isset($motivoReprogramado[$idmotivo])) {
                $verificarOpcion = $dataOpciones->listarOpcionesxVerificacion($idmodulo);
                $fecha = date("Y-m-d H:i:s");
                if (count($verificarOpcion) > 0) {
                    $Codigoverificacion=New Codigoverificacion();
                    $existeVerificacion = $Codigoverificacion->verificarCodigopendiente($idmodulo, $idactor, $idordenventa, $idmotivo, $fecha);
                    if (count($existeVerificacion) == 0) {

                        $nroCodigos = $_REQUEST['nroCodigos'];
                        $data['mensaje'] = '';

                        if ($nroCodigos>=1 && $nroCodigos<=4) {

                            for ($j=0; $j < $nroCodigos; $j++) { 
                            $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
                            $string = "";
                            for($i = 0; $i < 4; $i++) {
                                $string .= substr($caracteres, rand(0, strlen($caracteres) - 1), 1);
                            }
                            $dataGraba['idordenventa'] = $idordenventa;
                            $dataGraba['idactor'] = $idactor;
                            $dataGraba['idopciones'] = $idmodulo;
                            $dataGraba['idmotivo'] = $idmotivo;
                            $dataGraba['codigo'] = $string;
                            $dataGraba['descripcion'] = $txtdescripcion;
                            $dataGraba['uso'] = 0;
                            $nuevafecha = strtotime( '+'.$j.'5 minute', strtotime($fecha)) ;
                            $dataGraba['fechavencimiento'] = date('Y-m-d H:i:s', $nuevafecha);
                            $Codigoverificacion->graba($dataGraba);
                            $data['respuesta'] = 1;
                            $data['mensaje'] .= 'El código de verifación generado es: <span style="font-size:30px">' . $string . '</span>.</br>';
                            }
                        }else{
                            $data['respuesta'] = -1;
                            $data['mensaje'] .= 'Numero de codigos permitidos (min: 1 - max: 4)';
                        }

                    } else {
                        $data['respuesta'] = -1;
                        $data['mensaje'] = 'Ya se ha generado un código de verificación que aun esta pendiente con esos parametros, cual es: <span style="font-size:30px">' . $existeVerificacion[0]['codigo'] . '</span>.';
                    }
                } else {
                    $data['respuesta'] = -1;
                    $data['mensaje'] = 'El módulo solicitado no esta disponible.';
                }
            } else {
                $data['respuesta'] = -1;
                $data['mensaje'] = 'El mótivo seleccionado no existe.';
            }
        }
        $data['MotivoReprogramacion']=$motivoReprogramado;
        $data['OpcionesValidacion']=$dataOpciones->listarOpcionesxVerificacion();
        $data['UsuariosVerificacion']=$actorrol->actoresxRol(81);
        $this->view->show("creditos/generarcodigoverificacion.phtml", $data);
    }

    public function codigosgenerados() {
        $MotivoReprogramacion = $this->configIniTodo('MotivoReprogramacion');
        if ($_REQUEST['pagina'] >= 1) {
            $chkSinUso = $_REQUEST['chkUsar'];
            $chkProceso = $_REQUEST['chkProceso'];
            $chkUsadas = $_REQUEST['chkUsadas'];
            $chkVencidas = $_REQUEST['chkVencidas'];
            $txtFechaInicio = $_REQUEST['txtFechaInicio'];
            $txtFechaFin = $_REQUEST['txtFechaFin'];
            $idusuario = $_REQUEST['cmbUsuario'];
            $idmodulo = $_REQUEST['cmbModulo'];
            $idordenventa = $_REQUEST['idordenventa'];
            $idMotivo = $_REQUEST['idMotivo'];
            $pagina = $_REQUEST['pagina'];
            $Codigoverificacion=New Codigoverificacion();
            $dataListado = $Codigoverificacion->listarCodigoverificacion($chkSinUso, $chkProceso, $chkUsadas, $chkVencidas, $txtFechaInicio, $txtFechaFin, $idusuario, $idmodulo, $idordenventa, $idMotivo, $pagina);
            $tam = count($dataListado);
            $contenedor = '';
            $fecha_actual = strtotime(date("Y-m-d H:i:s", time()));
            for ($i = 0; $i < $tam; $i++) {
                $fechaVencimiento = strtotime($dataListado[$i]['fechavencimiento']);
                $estado = '';
                $eliminar = 0;
                if ($fechaVencimiento <= $fecha_actual && $dataListado[$i]['uso'] != 2) {
                    $estado = "VENCIDO";
                } else {
                    if ($dataListado[$i]['uso'] == 0) {
                        $eliminar = 1;
                        $estado = "PENDIENTE";
                    } else if ($dataListado[$i]['uso'] == 1) {
                        $eliminar = 1;
                        $estado = "VERIFICADO";
                    } else if ($dataListado[$i]['uso'] == 2) {
                        $estado = "USADO";
                    }
                }
                $contenedor .= '<tr>' .
                                    '<td>' . $dataListado[$i]['nombres'] . ' ' . $dataListado[$i]['apellidopaterno'] . ' ' . $dataListado[$i]['apellidomaterno'] . '</td>' .
                                    '<td style="text-align: center">' . $dataListado[$i]['usuario'] . '</td>' .
                                    '<td>' . $dataListado[$i]['nombre'] . ' :: ' . $dataListado[$i]['url'] . '</td>' .
                                    '<td style="text-align: center">' . $dataListado[$i]['codigov'] . '</td>' .
                                    '<td style="text-align: center">' . $MotivoReprogramacion[$dataListado[$i]['idmotivo']] . '</td>' .
                                    '<td style="text-align: center; font-weight: 550; font-size: 15px">' . $dataListado[$i]['codigo'] . '</td>' .
                                    '<td>' . $dataListado[$i]['fechacreacion'] . '</td>' .
                                    '<td>' . $dataListado[$i]['fechavencimiento'] . '</td>' .
                                    '<td style="text-align: center; font-weight: 550">' . $estado . '</td>' .
                                    '<td style="text-align: center;" class="classVer">' . (!empty($dataListado[$i]['descripcion']) ? '<a href="#" class="btnVerDescripcion" data-on="0" data-id="' . $dataListado[$i]['idcodigoverificacion'] . '"><img src="/imagenes/ver.gif"></a>' : '') . '</td>' .
                                    '<td style="text-align: center">' . ($eliminar == 1 ? '<a href="/creditos/codigosgenerados_eliminar/' . $dataListado[$i]['idcodigoverificacion'] . '" class="cllasEliminar"><img src="/imagenes/eliminar.gif"></a>' : '') . '</td>' .
                               '</tr>';
                if (!empty($dataListado[$i]['descripcion'])) {
                    $contenedor .= '<tr style="display: none;" id="trDescripcion' . $dataListado[$i]['idcodigoverificacion'] . '">' .
                                        '<td colspan="8" style="background: #e8f1fc; padding: 5px; border-bottom: 2px double black; border-right: 2px double black;">' .
                                            '<b>Descripción: </b>' . $dataListado[$i]['descripcion'] .
                                        '</td>' .
                                   '</tr><tr style="display: none;"></tr>';
                }
            }
            $htmlPaginacion = '<tr><td colspan="9" style="text-align: center; padding: 5px">';
            $paginacion=$Codigoverificacion->paginadoCodigoverificacion($chkSinUso, $chkProceso, $chkUsadas, $chkVencidas, $txtFechaInicio, $txtFechaFin, $idusuario, $idmodulo, $idordenventa, $idMotivo);
            $blockpaginas = round($paginacion / 10);
            if ($blockpaginas * 10 < $paginacion) {
                $blockpaginas = $blockpaginas + 1;
            } else {
                $blockpaginas = $blockpaginas;
            }
            if ($pagina > 1) {
                $htmlPaginacion .= "<a href='#' class='classPaginacion' data-page='" . ($pagina - 1) . "'> Anterior </a>";
            }
            for ($i = 1; $i <= $blockpaginas; $i++) {
                $max = $i * 10;
                for ($min = $max - 9; $min <= $max; $min++) {
                    if ($pagina >= $max - 9 && $pagina <= $max && $paginacion >= $min) {
                        if ($pagina == $min) {
                            $htmlPaginacion .= "<a href='#' class='classPaginacion' data-page='" . ($min) . "'> <b style='color:blue;'>" . ($min) . " </b></a>";
                        } else {
                            $htmlPaginacion .= "<a href='#' class='classPaginacion' data-page='" . ($min) . "'> " . ($min) . " </a>";
                        }
                    }
                }
            }
            if ($pagina < $paginacion && !empty($pagina)) {
                $htmlPaginacion .= "<a href='#' class='classPaginacion' data-page='" . ($pagina + 1) . "'> Siguiente </a>";
            }
            $htmlPaginacion .= ' <select id="cmbSeleccion">' .
                                    '<option value=""></option>';
        for ($i=1; $i <=$paginacion ; $i++) {
        $htmlPaginacion .= '<option value="' . $i .'">' . $i . '</option>';
            }
            $htmlPaginacion .= '</select>';
            $respuesta['paginacion'] = $htmlPaginacion . '</td></tr>';
            $respuesta['contenedor'] = $contenedor;
            echo json_encode($respuesta);
        } else {
            $data['MotivoReprogramacion'] = $MotivoReprogramacion;
            $actorrol=new actorrol();
            $dataOpciones=New Opciones();
            $data['OpcionesValidacion']=$dataOpciones->listarOpcionesxVerificacion();
            $data['UsuariosVerificacion']=$actorrol->actoresxRol(81);
            $this->view->show("creditos/codigosgenerados.phtml", $data);
        }
    }

    public function codigosgenerados_eliminar() {
        if ($_REQUEST['id'] > 0) {
            $idcodigoverificacion = $_REQUEST['id'];
            $Codigoverificacion = New Codigoverificacion();
            $existeVerificacion = $Codigoverificacion->verificarCodigopendiente2($idcodigoverificacion, date("Y-m-d H:i:s"));
            if (count($existeVerificacion) > 0) {
                $dataAct['estado'] = 0;
                $Codigoverificacion->actualiza($dataAct, $existeVerificacion[0]['idcodigoverificacion']);
                echo'<script type="text/javascript">
                        alert("El código de verificación ' . $existeVerificacion[0]['codigo'] . ' ha sido eliminado");
                     </script>';
            }
        }
        $this->codigosgenerados();
    }

    public function finalizarautorizacion() {
        if ($_REQUEST['idautorizacion'] > 0) {
            $idcodigoverificacion = $_REQUEST['idautorizacion'];
            $Codigoverificacion = New Codigoverificacion();
            $existeVerificacion = $Codigoverificacion->verificarCodigopendiente3($idcodigoverificacion, date("Y-m-d H:i:s"));
            if (count($existeVerificacion) > 0) {
                $dataAct['uso'] = 2;
                $Codigoverificacion->actualiza($dataAct, $existeVerificacion[0]['idcodigoverificacion']);
            }
        }
    }

    public function grabarClienteObservaciones() {
        $creditos = $this->AutoLoadModel('creditos');
        $url_idcliente=$_REQUEST['idcliente'];
        $url_cmbCondicionCompra=$_REQUEST['cmbCondicionCompra'];
        $url_cmbCalificacion=$_REQUEST['cmbCalificacion'];
        $url_txtObservacion=$_REQUEST['txtObservacion'];
        $motivo="condiciones";
        $grabarClienteObservaciones=$creditos->grabarClienteObservaciones($url_idcliente,$url_cmbCondicionCompra,$url_cmbCalificacion,$url_txtObservacion,$motivo);
        if($grabarClienteObservaciones){ $resultado=1; }else{ $resultado=0; }
        echo json_encode(array("resultado"=>$resultado));
    }
}

?>

