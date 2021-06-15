<?php

Class facturacioncontroller extends ApplicationGeneral {
    /* Generacion de factura */
    function generaFactura() {
        if (!empty($_REQUEST['id'])) {
            $model = $this->AutoLoadModel('ordenventa');
            $data['idordenventa'] = $_REQUEST['id'];
            $data['codigov'] = $model->sacarCodigo($_REQUEST['id']);
            $data['xdxd'] = 'xdxd';
        }
        $empresa = new Almacen();
        $data['Empresa'] = $empresa->listadoAlmacen();
        $data['ModoFacturacion'] = $this->modoFacturacion();
        $data['tipoDocumento'] = $this->tipoDocumento();
        if (count($_REQUEST) == 6) {
            $this->view->show('/facturacion/generacionfactura.phtml', $data);
        } else {
            //Generando el documento
            $dataFactura = $_REQUEST['Factura'];
            if (!empty($_REQUEST['xdxd'])) {
                $dataFactura['nombrecliente'] = $_REQUEST['txtNombreCliente'];
            }
            //validando que no exista y evitar volver a grabar cuando recargan la pagina
            $modelpdf = $this->AutoLoadModel('pdf');
            $exitofactura = $modelpdf->listaFacturaEmitidasNoAnuladas($dataFactura['idOrdenVenta']);
            if (count($exitofactura) == 0) {
                //$dataFactura['nombredoc']=1;
                $documento = new Documento();
                $movimiento = new Movimiento();
                $filtro = " idtipooperacion='1' and idordenventa='" . $dataFactura['idOrdenVenta'] . "'";
                $dataMovimiento = $movimiento->buscaMovimientoxfiltro($filtro);
                //codigo agregado recien
                $detalleOrdenVenta = new detalleOrdenVenta();
                $dataDetalles = $detalleOrdenVenta->listaDetalleOrdenVenta($dataFactura['idOrdenVenta']);
                $cantidadDetalles = count($dataDetalles);
                if (!empty($dataFactura['numdoc'])) {
                    $dataFactura['electronico'] = 0;
                    $maximoItem = $cantidadDetalles;
                } else {
                    $dataFactura['electronico'] = 1;
                    $limite = -1;
                    if ($_REQUEST['chkGenrarDocumento']) {
                        $limite = 1;
                    }
                    $maximoItem = $this->configIni("MaximoItem", "ItemFE") + ($limite == 1 ? 3 : 0);
                }
                //$correlativos = $cantidadDetalles/$maximoItem;                                
                $montofacturado = 0;
                $separador = 0;
                $dataFactura['hasta'] = 1;
                $dataFactura['desde'] = 1;
                $desde = 1;
                $hasta = 1;
                $tempMaximoItem = $maximoItem;
                if ($dataFactura['porcentajefactura'] == "")
                    $dataFactura['porcentajefactura'] = 100;
                for ($i = 0; $i < $cantidadDetalles; $i++) {
                    //echo $montofacturado . "<br>";
                    if ($separador == $tempMaximoItem) {
                        $tempMaximoItem = $maximoItem;
                        $dataFactura['desde'] = $desde;
                        $dataFactura['hasta'] = $hasta - 1;
                        //$dataFactura['numdoc'] = $documento->ultimoCorrelativoElectronico($dataFactura['serie'], $dataFactura['nombredoc']);
                        $dataFactura['montofacturado'] = $montofacturado;
                        $dataFactura['montoigv'] = $montofacturado - $montofacturado / 1.18;
                        
                        if ($montofacturado > 0) {
                            $id = $documento->grabaDocumento($dataFactura);
                        }
                        if (!empty($dataMovimiento) && $montofacturado > 0) {
                            $dataM['iddocumentotipo'] = 1;
                            $dataM['serie'] = $dataFactura['serie'];
                            //$dataM['ndocumento'] = $dataFactura['numdoc'];
                            $dataM['essunat'] = 1;
                            $exito = $movimiento->actualizaMovimiento($dataM, $filtro);
                        }
                        $montofacturado = 0;
                        $desde = $hasta;
                        $separador = 0;
                    }
                    $hasta++;
                    $dataDetalles[$i]['cantporcentaje'] = $dataDetalles[$i]['cantdespacho'] - $dataDetalles[$i]['cantdevuelta'];
                    if ($dataFactura['porcentajefactura'] != "") {
                        if ($modo == 1) {
                            $precio = $dataDetalles[$i]['preciofinal'];
                            $dataDetalles[$i]['preciofinal'] = (($precio * $dataFactura['porcentajefactura']) / 100);
                            $precioneto = round(($dataDetalles[$i]['preciofinal']), 2);
                            $dataDetalles[$i]['cantporcentaje'] = $dataDetalles[$i]['cantdespacho'] - $dataDetalles[$i]['cantdevuelta'];
                        } elseif ($modo == 2) {
                            $precioneto = round(($dataDetalles[$i]['preciofinal']), 2);
                            $cantidad = $dataDetalles[$i]['cantdespacho'] - $dataDetalles[$i]['cantdevuelta'];
                            $dataDetalles[$i]['cantporcentaje'] = round((($cantidad * $dataFactura['porcentajefactura']) / 100), 2);
                        } else {
                            $precioneto = round(($dataDetalles[$i]['preciofinal']), 2);
                        }
                    }
                    if ($dataFactura['electronico'] == 1) {
                        if ($dataDetalles[$i]['cantporcentaje'] > 0) {
                            if ($precioneto * $dataDetalles[$i]['cantporcentaje'] > 0.05) {
                                $montofacturado += ($precioneto * $dataDetalles[$i]['cantporcentaje']);
                                $tempMaximoItem++;
                            }
                            $separador++;
                        }
                    } else {
                        $montofacturado += ($precioneto * $dataDetalles[$i]['cantporcentaje']);
                        $separador++;
                    }
                }
                $dataFactura['desde'] = $desde;
                $dataFactura['hasta'] = $hasta - 1;
                /* if ($dataFactura['electronico'] == 1) {
                  $dataFactura['numdoc'] = $documento->ultimoCorrelativoElectronico($dataFactura['serie'], $dataFactura['nombredoc']);
                  } */
                $dataFactura['montofacturado'] = $montofacturado;
                $dataFactura['montoigv'] = $montofacturado - $montofacturado / 1.18;
                
                if ($montofacturado > 0) {
                    $id = $documento->grabaDocumento($dataFactura);
                }
                if (!empty($dataMovimiento)&&$montofacturado>0) {
                    $dataM['iddocumentotipo'] = 1;
                    $dataM['serie'] = $dataFactura['serie'];
                    $dataM['ndocumento'] = $dataFactura['numdoc'];
                    $dataM['essunat'] = 1;
                    $exito = $movimiento->actualizaMovimiento($dataM, $filtro);
                }
                $montofacturado = 0;
                $ov = New OrdenVenta();
                $dataOV = $_REQUEST['OrdenVenta'];
                $dataOV['esfacturado'] = 1;
                $ov->actualizaOrdenVenta($dataOV, $dataFactura['idOrdenVenta']);
                //actualizamos la serie y numero de del documento en la tabla movimiento
            }
            $this->view->show('/facturacion/generacionfactura.phtml', $data);
        }
    }

    function generapercepcion() {
        if (isset($_REQUEST['Percepcion'])) {
            $dataPercepcion = $_REQUEST['Percepcion'];
            $idDetalleOrdenCobro = $_REQUEST['txtIdDetalleOrdenCobro'];
            //echo $idDetalleOrdenCobro!='sin cargar gasto';die();
            $dataPercepcion['nombredoc'] = 10;
            $dataPercepcion['electronico'] = 1;
            $redondeo = $this->configIni('Globals', 'Redondeo');
            $documento = new Documento();
            $lstFacturas = $documento->getDetallePercepcion($dataPercepcion['idOrdenVenta'], " and d.esCargado=1 and d.idRelacionado=0 and d.esAnulado=0");
            //die();
            $tamano = count($lstFacturas);
            $montoPercepcion = 0;
            for ($i = 0; $i < $tamano; $i++) {/*
              if ($lstFacturas[$i]['porcentajefactura'] != "") {
              $lstFacturas[$i]['montofacturado'] = (($lstFacturas[$i]['montofacturado'] * $lstFacturas[$i]['porcentajefactura']) / 100);
              } */
                $montoPercepcion += $lstFacturas[$i]['montofacturado'] * $lstFacturas[$i]['percepcion'];
                $dataPercepcion['porcentajefactura'] = $lstFacturas[$i]['percepcion'] * 100;
            }
            $idPercepcion = 0;
            if ($montoPercepcion > 0) {
                $dataPercepcion['montofacturado'] = round($montoPercepcion, $redondeo);
                $idPercepcion = $documento->grabaDocumento($dataPercepcion);
            }
            if ($idPercepcion > 0) {
                $ordenCobro = $this->AutoLoadModel('ordencobro');
                $detalleOrdenCobro = $this->AutoLoadModel('detalleordencobro');

                if($idDetalleOrdenCobro !== 'sin cargar gasto'){
                    if ($idDetalleOrdenCobro <= 0) {
                        $dataOC['importeordencobro'] = round($montoPercepcion, $redondeo);
                        $dataOC['saldoordencobro'] = round($montoPercepcion, $redondeo);
                        $dataOC['escontado'] = 1;
                        $dataOC['idOrdenVenta'] = $dataPercepcion['idOrdenVenta'];
                        $dataOC['femision'] = date('Y-m-d');
                        $exitoOC = $ordenCobro->grabaOrdencobro($dataOC);
                        if ($exitoOC) {
                            $dataDOC['importedoc'] = round($montoPercepcion, $redondeo);
                            $dataDOC['saldodoc'] = round($montoPercepcion, $redondeo);
                            $dataDOC['formacobro'] = 1;
                            $dataDOC['tipogasto'] = 6;
                            $dataDOC['idordencobro'] = $exitoOC;
                            $dataDOC['fechagiro'] = date('Y-m-d');
                            $dataDOC['fvencimiento'] = date('Y-m-d');
                            $idDetalleOrdenCobro = $detalleOrdenCobro->grabaDetalleOrdenVentaCobro($dataDOC);
                            $idOrdenVenta = $dataPercepcion['idOrdenVenta'];
                        }
                    } else if ($idDetalleOrdenCobro > 0){
                        $dataBusquedaDOC = $detalleOrdenCobro->buscaDetalleOrdencobro($idDetalleOrdenCobro);
                        if (!empty($dataBusquedaDOC)) {
                            $numdocAc = $dataBusquedaDOC[0]['numeroletra'];
                            $idOrdenCobro = $dataBusquedaDOC[0]['idordencobro'];
                            $importeDoc = $dataBusquedaDOC[0]['importedoc'];
                            $saldoDoc = $dataBusquedaDOC[0]['saldodoc'];
                            $dataDOC['importedoc'] = round($importeDoc + $montoPercepcion, $redondeo);
                            $dataDOC['saldoDoc'] = round($saldoDoc + $montoPercepcion, $redondeo);
                            $dataDOC['tipogasto'] = 6;
                            $exitoDOC = $detalleOrdenCobro->actualizaDetalleOrdencobro($dataDOC, $idDetalleOrdenCobro);
                            if ($exitoDOC) {
                                if ($numdocAc != "") {
                                    $dataDoc2['montofacturado'] = $dataDOC['importedoc'];
                                    $exitoD = $documento->actualizarDocumento($dataDoc2, "nombredoc=7 and numdoc='$numdocAc'");
                                }
                                $dataBusquedaOC = $ordenCobro->buscaOrdencobro($idOrdenCobro);
                                if (!empty($dataBusquedaOC)) {
                                    $idOrdenVenta = $dataBusquedaOC[0]['idordenventa'];
                                    $importeOrdenCobro = $dataBusquedaOC[0]['importeordencobro'];
                                    $saldoOrdenCobro = $dataBusquedaOC[0]['saldoordencobro'];
                                    $dataOC['importeordencobro'] = round($importeOrdenCobro + $montoPercepcion, $redondeo);
                                    $dataOC['saldoordencobro'] = round($saldoOrdenCobro + $montoPercepcion, $redondeo);
                                    $exitoOC = $ordenCobro->actualizaOrdencobro($dataOC, $idOrdenCobro);
                                }
                            }
                        }
                    }
                }else if($idDetalleOrdenCobro === 'sin cargar gasto'){
                    $idDetalleOrdenCobro = 1;
                    $exitoOC = 1;
                }
                if ($exitoOC > 0 && $idDetalleOrdenCobro > 0) {
                    $ordenGasto = $this->AutoLoadModel('ordengasto');
                    $dataOrdenGasto = $ordenGasto->buscaxFiltro("idordenventa='$idOrdenVenta' and idtipogasto=6 and estado=1");
                    if (!empty($dataOrdenGasto)) {
                        $dataOG['importegasto'] = round($montoPercepcion + $dataOrdenGasto[0]['importegasto'], 2);
                        $exitoOG = $ordenGasto->actualiza($dataOG, $dataOrdenGasto[0]['idordengasto']);
                    } else {
                        $dataOG['importegasto'] = round($montoPercepcion, 2);
                        $dataOG['idordenventa'] = $idOrdenVenta;
                        $dataOG['idtipogasto'] = 6;
                        $exitoOG = $ordenGasto->graba($dataOG);
                    }
                    $dataNewPercepcion['idRelacionado'] = $idPercepcion;
                    for ($i = 0; $i < $tamano; $i++) {
                        $documento->actualizarDocumento($dataNewPercepcion, "iddocumento='" . $lstFacturas[$i]['iddocumento'] . "'");
                    }
                }
            }
        }
        $this->view->show('/facturacion/percepcion.phtml');
    }

    /* Generacion de guia de remision */

    function genguiaremi() {
        if (count($_REQUEST) == 6) {
            $this->view->show('/facturacion/generacionguiaremision.phtml');
        } else {
            $documento = new Documento();
            $ordenVenta = new OrdenVenta();
            $dataGuiaRemision = $_REQUEST['GuiaRemision'];
            $dataGuiaRemision['nombredoc'] = 4;
            $modelpdf = $this->AutoLoadModel('pdf');
            $exitofactura = $modelpdf->listaGuiasEmitidasNoAnuladas($dataGuiaRemision['idordenventa']);
            if (count($exitofactura) == 0) {
                //si usamos esto debemos grabar en la orden de venta
                //$dataOrdenVenta['guiaremision']=1;
                $idordenventa = $dataGuiaRemision['idordenventa'];
                $dataOrdenVenta = $_REQUEST['ordenVenta'];
                $dataOrdenVenta['guiaremision'] = 1;
                $exito = $ordenVenta->actualizaOrdenVenta($dataOrdenVenta, $idordenventa);
                if ($exito) {
                    $id = $documento->grabaDocumento($dataGuiaRemision);
                    $movimiento = new Movimiento();
                    $filtro = " idtipooperacion='1' and idordenventa='" . $idordenventa . "'";
                    $dataMovimiento = $movimiento->buscaMovimientoxfiltro($filtro);
                    if (!empty($dataMovimiento) and $dataMovimiento[0]['iddocumentotipo'] != 1 and $dataMovimiento[0]['iddocumentotipo'] != 2) {
                        $dataM['iddocumentotipo'] = 4;
                        $dataM['serie'] = $dataGuiaRemision['serie'];
                        $dataM['ndocumento'] = $dataGuiaRemision['numdoc'];
                        $dataM['essunat'] = 1;

                        $exito = $movimiento->actualizaMovimiento($dataM, $filtro);
                    }
                    $this->view->show('/facturacion/generacionguiaremision.phtml');
                }
            } else {
                $this->view->show('/facturacion/generacionguiaremision.phtml');
            }
        }
    }

    /* Nota de credito */

    function notacredito() {
        $_SESSION['Autenticado'] = true;
        $almacen = new Almacen();
        $data['Almacen'] = $almacen->listadoAlmacen();
        $this->view->show("facturacion/notacredito.phtml", $data);
    }

    function notadebito() {
        $data['sustentos'] = $this->configIniTodo('SustentoDebito');
        $_SESSION['Autenticado'] = true;
        $tipoGasto = $this->AutoLoadModel('tipogasto');
        $data['tipogasto'] = $tipoGasto->lista();
        $this->view->show("facturacion/notadebito.phtml", $data);
    }

    function monto() {
        $idorden = $_REQUEST['id'];
        $tipo = $_REQUEST['tipo'];
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $data = $ordenGasto->importeGasto($idorden, $tipo);
        echo json_encode(array('importe' => (!empty($data) ? $data : '0.00')));
    }

    function buscarletranotadebito() {
        $nroletra = $_REQUEST['term'];
        $codigoov = $_REQUEST['codigov'];
        $doc = new DetalleOrdenCobro();
        $data = $doc->letranotadebito($codigoov, $nroletra);
        echo json_encode($data);
    }

    function registrarnotadebito() {
        if ($_REQUEST['NotaDebito']) {
            $data = $_REQUEST['NotaDebito'];
            $Descripciones = $_REQUEST['Descripciones'];
            $Cantidades = $_REQUEST['Cantidades'];
            $Precios = $_REQUEST['Precios'];
            $data['nombredoc'] = 6;
            $data['electronico'] = 1;
            $documento = new Documento();
            $data['numdoc'] = $documento->ultimoCorrelativoElectronico($data['serie'], 6);
            $exito = $documento->grabaDocumento($data);
            if ($exito) {
                $debito['iddocumento'] = $documento->getIdDebitoXDetalle($data['idordenventa'], $data['serie'], $data['numdoc'], 6, $data['fechadoc']);
                echo "<br><br>";
                $debitomodel = new Notedebito();
                for ($i = 0; $i < count($Descripciones); $i++) {
                    $debito['descripcion'] = $Descripciones[$i];
                    $debito['cantidad'] = $Cantidades[$i];
                    $debito['preciouni'] = $Precios[$i];
                    $debitomodel->grabaDocumento($debito);
                }
                $this->notadebito();
            }
        } else {
            $this->notadebito();
        }
    }

    /* Emision de letras letras */

    function emiLetras() {
        $_SESSION['Autenticado'] = true;
        $ordenVenta = new OrdenVenta();
        $data['OrdenVenta'] = $ordenVenta->listarEmisionLetras();
        $data['CondicionLetra'] = $this->condicionLetra();
        $data['TipoLetra'] = $this->tipoLetra();
        $this->view->show("facturacion/emisionletras.phtml", $data);
    }

//Busqueda de transporte por cliente
    function buscatransporte() {
        $id = $_REQUEST['id'];
        $transporte = new Transporte();
        $cliente = new Cliente();
        $data = $transporte->buscarxCliente($id);
        //$dataCliente=$cliente->buscaCliente($id);
        for ($i = 0; $i < count($data); $i++) {
            /* if($data[$i]['idtransporte']==$dataCliente[0]['transporte']){
              echo '<option value="'.$data[$i]['idtransporte'].'" selected>'.$data[$i]['trazonsocial'];
              }else{ */
            echo '<option value="' . $data[$i]['idclientetransporte'] . '">' . $data[$i]['trazonsocial'];
            //}
        }
    }

    function generaLetras() {
        $NumeroOrdenVenta = $_REQUEST['ordenVenta'];
        $condicionLetras = $_REQUEST['condicionLetras'];
        $ordenVenta = new OrdenVenta();
        $dataOrdenVenta = $ordenVenta->buscarxid($NumeroOrdenVenta);
        $dataCondicionLetras = $this->buscaCondicionLetra($condicionLetras);
        $arrayCondicionLetras = explode("/", $dataCondicionLetras);
        $actualDate = date("d-m-Y");
        for ($i = 0; $i < $condicionLetras; $i++) {
            echo "<tr>";
            echo "<td>" . ($i + 1) . "</td>";
            echo "<td>" . date("d-m-Y", strtotime("$actualDate + " . $arrayCondicionLetras[$i] . " day")) . "</td>";
            echo "<td>" . number_format(($dataOrdenVenta[0]['importe'] / $condicionLetras), 2) . "</td>";
            echo "<tr>";
        }
    }

    function seguimientoLetras() {
        $this->view->show("/facturacion/seguimientoletras.phtml");
    }

    function listaProductosGuia() {
        $idGuia = $_REQUEST['idguia'];
        $tipodoc = $_REQUEST['tipo'];
        $serie = $_REQUEST['tipo'];
        $maximoItem = $this->configIni("MaximoItem", "ItemFE");
        $contenidodetalle = "";
        $dataGuia = $this->AutoLoadModel("OrdenVenta");
        $dataDocu = $this->AutoLoadModel("Documento");
        $idTipoCambio = $dataGuia->BuscarCampoOVxId($idGuia, "IdTipoCambioVigente"); //PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA
        $TipoCambio = $this->AutoLoadModel("TipoCambio");
        $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
        $simboloMoneda = $dataTipoCambio[0]['simbolo'];
        $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
        $porcentaje = $_REQUEST['porcentaje'];
        $modo = $_REQUEST['modo'];
        $detalleOrdenVenta = new detalleOrdenVenta();
        $almacen = new Almacen();
        $data = $detalleOrdenVenta->listaDetalleOrdenVenta($idGuia);
        $total = 0;
        $descuento = New Descuento();
        $dataDescuento = $descuento->listado();
        $dataAlmacen = $almacen->listado();
        $cantidadDescuento = count($dataDescuento);
        $cantidadAlmacen = count($dataAlmacen);
        $cantidadDetalles = count($data);
        for ($i = 0; $i < $cantidadDescuento; $i++) {
            $dscto[$dataDescuento[$i]['id']] = $dataDescuento[$i]['valor'];
        }
        for ($x = 0; $x < $cantidadAlmacen; $x++) {
            $dataAlmacen[$x]['importe'] = 0;
        }
        $varTotal = 0;
        $separador = 0;
        $ultimoCorrelativo = $dataDocu->ultimoCorrelativoElectronico($serie, $tipodoc);
        $nrodocumento = 2;
        $correlativos = $cantidadDetalles / $maximoItem;
        $contenidodetalle .= "<tr>";
        $contenidodetalle .= "<th colspan='11' style='background: #000000; color: white;'><center>DOCUMENTO 1</center></th>";
        $contenidodetalle .= "</tr>";
        $importexdocumento = 0;
        $nroItemTemp = 0;
        $tempMaximoItem = $maximoItem;
        for ($i = 0; $i < $cantidadDetalles; $i++) {
            if ($separador == $tempMaximoItem) {
                $tempMaximoItem = $maximoItem;
                $contenidodetalle .= "<tr>";
                $contenidodetalle .= '<th colspan="9" style="color: black; background: #77c4e2; text-align:right;">IMPORTE A PAGAR:</th>';
                $contenidodetalle .= '<td style="color: black; font-weight: 500;">' . $simboloMoneda . ' ' . number_format($importexdocumento, 2) . '</td>';
                $contenidodetalle .= '<td></td>';
                $contenidodetalle .= "</tr>";
                $contenidodetalle .= "<tr>";
                $contenidodetalle .= "<th colspan='11' style='background: #000000; color: white;'><center>DOCUMENTO " . $nrodocumento . "</center></th>";
                $contenidodetalle .= "</tr>";
                $separador = 0;
                $nrodocumento++;
                $importexdocumento = 0;
            }
            $data[$i]['cantporcentaje'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
            if ($porcentaje != "") {
                if ($modo == 1) {
                    $precio = $data[$i]['preciofinal'];
                    $data[$i]['preciofinal'] = (($precio * $porcentaje) / 100);
                    $data[$i]['cantporcentaje'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                } else {
                    $cantidad = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                    $data[$i]['cantporcentaje'] = ceil(($cantidad * $porcentaje) / 100);
                }
            }
            for ($x = 0; $x < $cantidadAlmacen; $x++) {
                if ($dataAlmacen[$x]['idalmacen'] == $data[$i]['idalmacen']) {
                    $subtotal = ($data[$i]['preciofinal'] * ($data[$i]['cantporcentaje']));
                    $dataAlmacen[$x]['importe'] += $subtotal;
                    $varTotal += $subtotal;
                }
            }
            if ($data[$i]['cantporcentaje'] > 0) {
                if ($data[$i]['preciofinal'] * $data[$i]['cantporcentaje'] <= 0.05) {
                    $data[$i]['preciofinal'] = 0;
                }
                $precioneto = (number_format($data[$i]['preciofinal'], 2));
                if ($data[$i]['preciofinal'] == 0) {
                    $contenidodetalle .= '<tr style="background: #1570d7; color: white" title="No se incluira en el documento electronico">';
                } else {
                    $contenidodetalle .= "<tr>";
                }
                $separador++;
                $contenidodetalle .= '<td>' . $separador . '</td>';
                $precioTotal = $precioneto * ($data[$i]['cantporcentaje']);
                $nroItemTemp++;
            } else {
                $contenidodetalle .= "<tr style='background: #ff7d7d'>";
                $contenidodetalle .= '<td>-</td>';
                $precioneto = 0;
                $precioTotal = 0;
            }
            //$precioTotal=(($data[$i]['precioaprobado'])*($data[$i]['cantaprobada'])-($data[$i]['tdescuentoaprovado']));
            $contenidodetalle .= '<td>' . $data[$i]['codigov'] . '</td>';
            $contenidodetalle .= '<td>' . $data[$i]['nompro'] . '</td>';
            $contenidodetalle .= '<td>' . ($data[$i]['cantdespacho']) . '</td>';
            $contenidodetalle .= '<td>' . ($data[$i]['cantdevuelta']) . '</td>';
            $contenidodetalle .= '<td>' . ($data[$i]['cantporcentaje']) . '</td>';
            $contenidodetalle .= '<td> ' . $simboloMoneda . ' ' . number_format($data[$i]['preciolista2'], 2) . '</td>';
            $contenidodetalle .= '<td>' . $dscto[$data[$i]['descuentoaprobado']] . '</td>';
            $contenidodetalle .= '<td style="text-align:right;">' . $simboloMoneda . ' ' . number_format($precioneto, 2) . '</td>';
            $contenidodetalle .= '<td style="text-align:right;">' . $simboloMoneda . ' ' . number_format($precioTotal, 2) . '</td>';
            if ($data[$i]['preciofinal'] == 0) {
                $tempMaximoItem++;
                $contenidodetalle .= '<td><img src="/imagenes/iconos/regalo.png" width="20px"></td>';
            } else {
                $contenidodetalle .= '<td></td>';
            }
            $contenidodetalle .= "</tr>";
            $importexdocumento += $precioTotal;
            $total += $precioTotal;
        }
        $contenidodetalle .= "<tr>";
        $contenidodetalle .= '<th colspan="9" style="color: black; background: #77c4e2; text-align:right;">IMPORTE A PAGAR:</th>';
        $contenidodetalle .= '<td style="color: black; font-weight: 500;">' . $simboloMoneda . ' ' . number_format($importexdocumento, 2) . '</td>';
        $contenidodetalle .= '<td></td>';
        $contenidodetalle .= "</tr>";
        $contenidodetalle .= "<tr>";
        $contenidodetalle .= "<th colspan='11'><center style='background: #000000; color: white;'>IMPORTE TOTAL A FACTURAR</center></th>";
        $contenidodetalle .= "</tr>";
        $contenidodetalle .= '<tr style="color:#f00">';
        $contenidodetalle .= '<td colspan="9" class="right bold" style="text-align:right;">
					Precio de Venta<br>
					I.G.V.<br>
					Total a Pagar
				</td>';
        $contenidodetalle .= '<td style="text-align:right;">' . $simboloMoneda . ' ' .
                number_format(($total / 1.18), 2) . '<br>' . $simboloMoneda . ' ' .
                number_format($total - ($total / 1.18), 2) . '<br>' . $simboloMoneda . ' ' .
                number_format(($total), 2) .
                '</td>';
        $contenidodetalle .= '<td></td>';
        $contenidodetalle .= "</tr>.<input type='hidden' name='Factura[montoigv]' value='" . (number_format($total - ($total / 1.18), 2)) . "'>";
        $contenidodetalle .= "</tr>.<input type='hidden' name='Factura[montofacturado]' value='" . ($total) . "'>";
        $contenidodetalle .= "<tr><td colspan='11'><table>";
        $contenidodetalle .= "<th>Empresa</th><th>Importe (" . $simboloMoneda . " )</th><th>Porcentaje (%)</th>";
        for ($x = 0; $x < $cantidadAlmacen; $x++) {
            if ($dataAlmacen[$x]['importe'] != 0) {
                $valor = (($dataAlmacen[$x]['importe'] / $varTotal) * 100);
                $contenidodetalle .= "<tr><td>" . $dataAlmacen[$x]['razsocalm'] . "</td><td>" . number_format($dataAlmacen[$x]['importe'], 2) . "</td><td>" . round($valor, 2) . "</td></tr>";
            }
        }
        $TXtCorrelativo = "";
        for ($i = 0; $i < $correlativos; $i++) {
            $TXtCorrelativo .= '<li>' .
                    '<label>Correlativo ' . ($i + 1) . ':</label>' .
                    '<input type="text" maxlength="10" size="10" name="Factura[numdoc]" required="required" value="' . str_pad($ultimoCorrelativo, 8, "0", STR_PAD_LEFT) . '" disabled="">' .
                    '</li>';
            $ultimoCorrelativo++;
        }
        $contenidodetalle .= "</table></td></tr>";
        header('Content-type: application/json; charset=utf-8');
        $resp['chkUnion'] = '';
        if ($nroItemTemp > $maximoItem && $nroItemTemp <= $maximoItem + 3) {
            $resp['chkUnion'] = '<input type="checkbox" name="chkGenrarDocumento" id="chkGenrarDocumento"> Ampliar capacidad a ' . ($maximoItem + 3) . ' items';
        }
        $resp['correlativo'] = $TXtCorrelativo;
        $resp['detalleOV'] = $contenidodetalle;
        $resp['registrar'] = ($total > 0 ? 1 : 0);
        echo json_encode($resp);
    }

    function actualizarCorrelativos() {
        $tipo = $_REQUEST['tipo'];
        $serie = $_REQUEST['serie'];
        $idGuia = $_REQUEST['idguia'];
        $limit = $_REQUEST['limite'];
        $dataDocu = $this->AutoLoadModel("Documento");
        if (!empty($idGuia)) {
            $maximoItem = $this->configIni("MaximoItem", "ItemFE") + ($limit == 1 ? 3 : 0);
            $detalleOrdenVenta = new detalleOrdenVenta();
            $data = $detalleOrdenVenta->listaDetalleOrdenVenta($idGuia);
            $cantidadDetalles = 0;
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['preciofinal']*($data[$i]['cantdespacho'] - $data[$i]['cantdevuelta']) <= 0.05) {
                } else {
                    $cantidadDetalles++;
                }
            }
            $correlativos = $cantidadDetalles / $maximoItem;
        } else {
            $correlativos = 1;
        }
        $ultimoCorrelativo = $dataDocu->ultimoCorrelativoElectronico($serie, $tipo);
        for ($i = 0; $i < $correlativos; $i++) {
            echo '<li>' .
            '<label>Correlativo ' . ($i + 1) . ':</label>' .
            '<input type="text" maxlength="10" size="10" name="Factura[numdoc]" required="required" value="' . str_pad($ultimoCorrelativo, 8, "0", STR_PAD_LEFT) . '" disabled="">' .
            '</li>';
            $ultimoCorrelativo++;
        }
    }

    function listaProductosGuiaRecuperado() {
        $idGuia = $_REQUEST['id'];
        $dataGuia = $this->AutoLoadModel("OrdenVenta");
        $idTipoCambio = $dataGuia->BuscarCampoOVxId($idGuia, "IdTipoCambioVigente"); //PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA

        $TipoCambio = $this->AutoLoadModel("TipoCambio");
        $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
        $simboloMoneda = $dataTipoCambio[0]['simbolo'];
        $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
        $porcentaje = $_REQUEST['porcentaje'];
        $modo = $_REQUEST['modo'];
        $limite = $_REQUEST['limite'];
        $detalleOrdenVenta = new detalleOrdenVenta();
        $data = $detalleOrdenVenta->listaDetalleOrdenVenta($idGuia);
        $total = 0;
        $descuento = New Descuento();
        $almacen = New Almacen();
        $dataDescuento = $descuento->listado();
        $dataAlmacen = $almacen->listado();
        $cantidadDescuento = count($dataDescuento);
        $cantidadAlmacen = count($dataAlmacen);
        $cantidadDetalles = count($data);
        for ($i = 0; $i < $cantidadDescuento; $i++) {
            $dscto[$dataDescuento[$i]['id']] = $dataDescuento[$i]['valor'];
        }
        for ($x = 0; $x < $cantidadAlmacen; $x++) {
            $dataAlmacen[$x]['importe'] = 0;
        }
        $varTotal = 0;
        $maximoItem = $this->configIni("MaximoItem", "ItemFE") + ($limite == 1 ? 3 : 0);
        $nrodocumento = 2;
        $separador = 0;
        $contenidodetalle = "<tr>";
        $contenidodetalle .= "<th colspan='11'><center style='background: #000000; color: white;'>DOCUMENTO 1</center></th>";
        $contenidodetalle .= "</tr>";
        $importexdocumento = 0;
        $tempMaximoItem = $maximoItem;
        for ($i = 0; $i < $cantidadDetalles; $i++) {
            if ($separador == $tempMaximoItem) {
                $contenidodetalle .= "<tr>";
                $contenidodetalle .= '<th colspan="9" style="color: black; background: #77c4e2; text-align:right;">IMPORTE A PAGAR:</th>';
                $contenidodetalle .= '<td style="color: black; font-weight: 500;">' . $simboloMoneda . ' ' . number_format($importexdocumento, 2) . '</td>';
                $contenidodetalle .= '<td></td>';
                $contenidodetalle .= "</tr>";
                $contenidodetalle .= "<tr>";
                $contenidodetalle .= "<th colspan='11'><center style='background: #000000; color: white;'>DOCUMENTO " . $nrodocumento . "</center></th>";
                $contenidodetalle .= "</tr>";
                $separador = 0;
                $nrodocumento++;
                $importexdocumento = 0;
            }
            $data[$i]['cantporcentaje'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
            if ($porcentaje != "") {
                if ($modo == 1) {
                    $precio = $data[$i]['preciofinal'];
                    $data[$i]['preciofinal'] = (($precio * $porcentaje) / 100);
                    $data[$i]['cantporcentaje'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                } else {
                    $cantidad = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                    $data[$i]['cantporcentaje'] = ceil(($cantidad * $porcentaje) / 100);
                }
            }
            for ($x = 0; $x < $cantidadAlmacen; $x++) {
                if ($dataAlmacen[$x]['idalmacen'] == $data[$i]['idalmacen']) {
                    $subtotal = ($data[$i]['preciofinal'] * ($data[$i]['cantporcentaje']));
                    $dataAlmacen[$x]['importe'] += $subtotal;
                    $varTotal += $subtotal;
                }
            }
            if ($data[$i]['cantporcentaje'] > 0) {
                if ($data[$i]['preciofinal'] * $data[$i]['cantporcentaje'] <= 0.05) {
                    $data[$i]['preciofinal'] = 0;
                }
                $precioneto = (number_format($data[$i]['preciofinal'], 2));
                
                if ($data[$i]['preciofinal'] == 0) {
                    $contenidodetalle .= '<tr style="background: #1570d7; color: white" title="No se incluira en el documento electronico">';
                } else {
                    $contenidodetalle .= "<tr>";
                }
                
                $separador++;
                $contenidodetalle .= '<td>' . $separador . '</td>';
                $precioTotal = $precioneto * ($data[$i]['cantporcentaje']);
            } else {
                $contenidodetalle .= "<tr style='background: #ff7d7d'>";
                $contenidodetalle .= '<td>-</td>';
                $precioneto = 0;
                $precioTotal = 0;
            }
            //$precioTotal=(($data[$i]['precioaprobado'])*($data[$i]['cantaprobada'])-($data[$i]['tdescuentoaprovado']));
            $contenidodetalle .= '<td>' . $data[$i]['codigov'] . '</td>';
            $contenidodetalle .= '<td>' . $data[$i]['nompro'] . '</td>';
            $contenidodetalle .= '<td>' . ($data[$i]['cantdespacho']) . '</td>';
            $contenidodetalle .= '<td>' . ($data[$i]['cantdevuelta']) . '</td>';
            $contenidodetalle .= '<td>' . ($data[$i]['cantporcentaje']) . '</td>';
            $contenidodetalle .= '<td> ' . $simboloMoneda . ' ' . number_format($data[$i]['preciolista2'], 2) . '</td>';
            $contenidodetalle .= '<td>' . $dscto[$data[$i]['descuentoaprobado']] . '</td>';
            $contenidodetalle .= '<td style="text-align:right;">' . $simboloMoneda . ' ' . number_format($precioneto, 2) . '</td>';
            $contenidodetalle .= '<td style="text-align:right;">' . $simboloMoneda . ' ' . number_format($precioTotal, 2) . '</td>';
            if ($data[$i]['preciofinal'] == 0) {
                $tempMaximoItem++;
                $contenidodetalle .= '<td><img src="/imagenes/iconos/regalo.png" width="20px"></td>';
            } else {
                $contenidodetalle .= '<td></td>';
            }
            $contenidodetalle .= "</tr>";
            $importexdocumento += $precioTotal;
            $total += $precioTotal;
        }
        $contenidodetalle .= "<tr>";
        $contenidodetalle .= '<th colspan="9" style="color: black; background: #77c4e2; text-align:right;">IMPORTE A PAGAR:</th>';
        $contenidodetalle .= '<td style="color: black; font-weight: 500;">' . $simboloMoneda . ' ' . number_format($importexdocumento, 2) . '</td>';
        $contenidodetalle .= '<td></td>';
        $contenidodetalle .= "</tr>";
        $contenidodetalle .= "<tr>";
        $contenidodetalle .= "<th colspan='11'><center style='background: #000000; color: white;'>IMPORTE TOTAL A FACTURAR</center></th>";
        $contenidodetalle .= "</tr>";
        $contenidodetalle .= '<tr style="color:#f00">';
        $contenidodetalle .= '<td colspan="9" class="right bold" style="text-align:right;">
					Precio de Venta<br>
					I.G.V.<br>
					Total a Pagar
				</td>';
        $contenidodetalle .= '<td class="right">' . $simboloMoneda . ' ' .
                number_format(($total / 1.18), 2) . '<br>' . $simboloMoneda . ' ' .
                number_format($total - ($total / 1.18), 2) . '<br>' . $simboloMoneda . ' ' .
                number_format(($total), 2) .
                '</td>';
        $contenidodetalle .= '<td></td>';
        $contenidodetalle .= "</tr>.<input type='hidden' name='Factura[montoigv]' value='" . (number_format($total - ($total / 1.18), 2)) . "'>";
        $contenidodetalle .= "</tr>.<input type='hidden' name='Factura[montofacturado]' value='" . ($total) . "'>";

        $contenidodetalle .= "<tr><td colspan='11'><table>";
        $contenidodetalle .= '<th>Empresa</th><th>Importe (' . $simboloMoneda . ' )</th><th>Porcentaje (%)</th>';
        for ($x = 0; $x < $cantidadAlmacen; $x++) {
            if ($dataAlmacen[$x]['importe'] != 0) {
                $valor = (($dataAlmacen[$x]['importe'] / $varTotal) * 100);
                $contenidodetalle .= "<tr><td>" . $dataAlmacen[$x]['razsocalm'] . "</td><td>" . number_format($dataAlmacen[$x]['importe'], 2) . "</td><td>" . round($valor, 2) . "</td></tr>";
            }
        }
        $contenidodetalle .= "</table></td></tr>";
        header('Content-type: application/json; charset=utf-8');
        $resp['detalleOV'] = $contenidodetalle;
        $resp['registrar'] = ($total > 0 ? 1 : 0);
        echo json_encode($resp);
    }

    function listaProductosGuiaRemision() {
        $idGuia = $_REQUEST['id'];
        $detalleOrdenVenta = new DetalleOrdenVenta();
        $data = $detalleOrdenVenta->listaDetalleOrdenVenta($idGuia);
        $unidadMedida = $this->unidadMedida();
        for ($i = 0; $i < count($data); $i++) {
            echo "<tr>";
            echo '<td>' . ($i + 1) . '</td>';
            echo '<td>' . $data[$i]['codigov'] . '</td>';
            echo '<td>' . $data[$i]['nompro'] . '</td>';
            echo '<td>' . $unidadMedida[($data[$i]['unidadmedida'])] . '</td>';
            echo '<td>' . $data[$i]['cantdespacho'] . '</td>';
            echo "</tr>";
        }
    }

    function autocompletefacturaelectronica() {
        $texIni = $_REQUEST['term'];
        $documento = new documento();
        $data = $documento->autocompletefacturaelectronica($texIni);
        echo json_encode($data);
    }

    function autocompletefactura() {
        $texIni = $_REQUEST['term'];
        $documento = new documento();
        $data = $documento->autocompletefactura($texIni);
        echo json_encode($data);
    }

    function registraNotaCredito() {
        $data = $_REQUEST['NotaCredito'];
        $idcliente = $_REQUEST['idcliente'];
        $movimiento = $_REQUEST['opcmovimiento'];
        $data['nombredoc'] = 5;
        //el concepto 2 es cuando es por precio y 1 es cuando es por devolucion
        $documento = new Documento();
        $ingresos = new Ingresos();
        $data['electronico'] = 1;
        if ($_REQUEST['nc_fisico']) {
            $data['electronico'] = 0;
            $data['serie'] = 1;
        }
        //$data['numdoc'] = $documento->ultimoCorrelativoElectronico ($data['serie'], 5);
        $data['nroSeleccion'] = $data['nroSeleccion'] - 1;
        $exito = $documento->grabaDocumento($data);
        if ($exito) {
            if ($movimiento == 2) {
                //creamos in ingreso
                $dataIngreso['idordenventa'] = $data['idordenventa'];
                $dataIngreso['idcliente'] = $idcliente;
                $dataIngreso['montoingresado'] = $data['montofacturado'];
                $dataIngreso['saldo'] = $data['montofacturado'];
                $dataIngreso['tipocobro'] = 10;
                $dataIngreso['idcobrador'] = 398;
                $dataIngreso['esvalidado'] = 1;
                $dataIngreso['nrodoc'] = $data['numdoc'];
                $dataIngreso['fcobro'] = date("Y-m-d");
                $graba = $ingresos->graba($dataIngreso);
            }
            $ruta['ruta'] = "/facturacion/notacredito";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function listaOrdenVenta() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = 1;
        }
        session_start();
        $_SESSION['P_ListaOrden'] = "";
        $model = $this->AutoLoadModel('pdf');
        $ordencobro = $this->AutoLoadModel('ordencobro');
        $Factura = $model->listaOrdenVentaPaginado($id, "");
        for ($i = 0; $i < count($Factura); $i++) {
            $documento = $model->listaFacturaEmitidas($Factura[$i]['idordenventa']);
            $Factura[$i]['importeguia'] = $ordencobro->deudatotal($Factura[$i]['idordenventa']);
            $Factura[$i]['deuda'] = $ordencobro->totalPendiente($Factura[$i]['idordenventa']);
            if (!empty($documento) && count($documento) == 1) {
                $Factura[$i]['serie'] = $documento[0]['serie'];
                $Factura[$i]['numdoc'] = $documento[0]['numdoc'];
                $Factura[$i]['montofacturado'] = $documento[0]['montofacturado'];
                $Factura[$i]['nombredoc'] = $documento[0]['nombredoc'];
                $Factura[$i]['iddocumento'] = $documento[0]['iddocumento'];
            }
        }
        $data['Factura'] = $Factura;
        $paginacion = $model->paginadoOrdenVenta("");
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        //echo '<pre>';
        //print_r($Factura);
        //exit;
        $this->view->show('/facturacion/listaordenventa.phtml', $data);
    }

    function filtro() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaOrdenxPagarEstadoLetra($texIni);
        echo json_encode($data);
    }

    function filtroDespacho() {
        $id = $_REQUEST['id'];
        $model = $this->AutoLoadModel('pdf');
        $filtro = "wc_ordenventa.idordenventa=$id";
        $Factura = $model->listaOrdenVentaProd($filtro);
        $transporte = $model->nombretransporte($Factura[0]['idclientetransporte']);
        $data['codigov'] = $Factura[0]['codigov'];
        $data['simbolomoneda'] = $Factura[0]['simbolomoneda'];
        $data['importeov'] = number_format($Factura[0]['importeov'], 2);
        $data['nombres'] = $Factura[0]['nombres'];
        $data['apellidopaterno'] = $Factura[0]['apellidopaterno'];
        $data['razonsocial'] = $Factura[0]['razonsocial'];
        $data['observaciones'] = html_entity_decode($Factura[0]['observaciones'], ENT_QUOTES);
        $data['apellidomaterno'] = $Factura[0]['apellidomaterno'];
        $data['idordenventa'] = $Factura[0]['idordenventa'];
        $data['despacho_prod'] = $Factura[0]['despacho_prod'];
        $data['confirmacion_prod'] = $Factura[0]['confirmacion_prod'];
        $data['despacho_prod2'] = $Factura[0]['despacho_prod2'];
        $data['despacho_prod3'] = $Factura[0]['despacho_prod3'];
        $data['observacion_entregaprod'] = $Factura[0]['observacion_entregaprod'];
        $data['nrocajas'] = $Factura[0]['nrocajas'];
        $data['fechadespachado'] = $Factura[0]['fechadespachado'];
        $data['anulado'] = $Factura[0]['anulado'];
        $data['transporte'] = $transporte;
        echo json_encode($data);
    }

    function despacho() {
        session_start();
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_ListaOrden'] = $_REQUEST['txtBusqueda'];
            $parametro = $_SESSION['P_ListaOrden'];
            $filtro = "wc_ordenventa.`codigov` like '%$parametro%'";
        }
        if (!empty($_REQUEST['listar'])) {
            $filtro = "";
        }
        $model = $this->AutoLoadModel('pdf');
        $ordencobro = $this->AutoLoadModel('ordencobro');
        $Factura = $model->listaOrdenVentaPaginado($id, $filtro);
        for ($i = 0; $i < count($Factura); $i++) {
            $documento = $model->listaFacturaEmitidas($Factura[$i]['idordenventa']);
            $Factura[$i]['nombreTranporte'] = $model->nombretransporte($Factura[$i]['idclientetransporte']);
            $Factura[$i]['importeguia'] = $ordencobro->deudatotal($Factura[$i]['idordenventa']);
            $Factura[$i]['deuda'] = $ordencobro->totalPendiente($Factura[$i]['idordenventa']);
            if (!empty($documento) && count($documento) == 1) {
                $Factura[$i]['serie'] = $documento[0]['serie'];
                $Factura[$i]['numdoc'] = $documento[0]['numdoc'];
                $Factura[$i]['montofacturado'] = $documento[0]['montofacturado'];
                $Factura[$i]['nombredoc'] = $documento[0]['nombredoc'];
                $Factura[$i]['iddocumento'] = $documento[0]['iddocumento'];
            }
        }
        $data['Factura'] = $Factura;
        $paginacion = $model->paginadoOrdenVenta("");
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show('/facturacion/despacho.phtml', $data);
    }

    function seguimientoSeguridad() {
        session_start();
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_ListaOrden'] = $_REQUEST['txtBusqueda'];
            $parametro = $_SESSION['P_ListaOrden'];
            $filtro = "wc_ordenventa.`codigov` like '%$parametro%'";
        }
        if (!empty($_REQUEST['listar'])) {
            $filtro = "";
        }
        $model = $this->AutoLoadModel('pdf');
        $ordencobro = $this->AutoLoadModel('ordencobro');
        $Factura = $model->listaOrdenVentaPaginado($id, $filtro);
        for ($i = 0; $i < count($Factura); $i++) {
            $documento = $model->listaFacturaEmitidas($Factura[$i]['idordenventa']);
            $Factura[$i]['nombreTranporte'] = $model->nombretransporte($Factura[$i]['idclientetransporte']);
            $Factura[$i]['importeguia'] = $ordencobro->deudatotal($Factura[$i]['idordenventa']);
            $Factura[$i]['deuda'] = $ordencobro->totalPendiente($Factura[$i]['idordenventa']);
            if (!empty($documento) && count($documento) == 1) {
                $Factura[$i]['serie'] = $documento[0]['serie'];
                $Factura[$i]['numdoc'] = $documento[0]['numdoc'];
                $Factura[$i]['montofacturado'] = $documento[0]['montofacturado'];
                $Factura[$i]['nombredoc'] = $documento[0]['nombredoc'];
                $Factura[$i]['iddocumento'] = $documento[0]['iddocumento'];
            }
        }
        $data['Factura'] = $Factura;
        $paginacion = $model->paginadoOrdenVenta("");
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show('/facturacion/seguimientoSeguridad.phtml', $data);
    }

    function buscaOrdenVenta() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = 1;
        }
        session_start();
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_ListaOrden'] = $_REQUEST['txtBusqueda'];
        }
        $parametro = $_SESSION['P_ListaOrden'];
        $filtro = "wc_ordenventa.`codigov` like '%$parametro%'";

        $model = $this->AutoLoadModel('pdf');
        $Factura = $model->listaOrdenVentaPaginado($id, $filtro);
        for ($i = 0; $i < count($Factura); $i++) {
            $documento = $model->listaFacturaEmitidas($Factura[$i]['idordenventa']);

            if (!empty($documento) && count($documento) == 1) {
                $Factura[$i]['serie'] = $documento[0]['serie'];
                $Factura[$i]['numdoc'] = $documento[0]['numdoc'];
                $Factura[$i]['montofacturado'] = $documento[0]['montofacturado'];
                $Factura[$i]['nombredoc'] = $documento[0]['nombredoc'];
                $Factura[$i]['iddocumento'] = $documento[0]['iddocumento'];
            }
        }
        $data['Factura'] = $Factura;
        $paginacion = $model->paginadoOrdenVenta($filtro);
        $data['retorno'] = $parametro;
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['totregistros'] = $model->cuentaOrdenVenta($filtro);
        $this->view->show('/facturacion/buscaordenventa.phtml', $data);
    }

    function buscaDuracion() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = 1;
        }
        session_start();
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_ListaOrden'] = $_REQUEST['txtBusqueda'];
        }
        $parametro = $_SESSION['P_ListaOrden'];
        $filtro = "wc_ordenventa.`codigov` like '%$parametro%'";
        $model = $this->AutoLoadModel('pdf');
        $ordenventaduracion = $this->AutoLoadModel('ordenventaduracion');
        $Factura = $model->listaOrdenVentaPaginado($id, $filtro);
        for ($i = 0; $i < count($Factura); $i++) {
            $documento = $model->listaFacturaEmitidas($Factura[$i]['idordenventa']);
            $ovd = $ordenventaduracion->listaOrdenVentaDuracionxOrdenVenta($Factura[$i]['idordenventa']);
            $cantidadDuracion = count($ovd);
            for ($y = 0; $y < $cantidadDuracion; $y++) {
                if (strcmp($ovd[$y]['referencia'], "ventas") == 0) {
                    $Factura[$i]['dVentas'] = $ovd[$y]['tiempo'];
                } elseif (strcmp($ovd[$y]['referencia'], "cobranza") == 0) {
                    $Factura[$i]['dCobranza'] = $ovd[$y]['tiempo'];
                } elseif (strcmp($ovd[$y]['referencia'], "almacen") == 0) {
                    $Factura[$i]['dAlmacen'] = $ovd[$y]['tiempo'];
                } elseif (strcmp($ovd[$y]['referencia'], "credito") == 0) {
                    $Factura[$i]['dCredito'] = $ovd[$y]['tiempo'];
                } elseif (strcmp($ovd[$y]['referencia'], "despacho") == 0) {
                    $Factura[$i]['dDespacho'] = $ovd[$y]['tiempo'];
                }
            }
            if (!empty($documento) && count($documento) == 1) {
                $Factura[$i]['serie'] = $documento[0]['serie'];
                $Factura[$i]['numdoc'] = $documento[0]['numdoc'];
                $Factura[$i]['montofacturado'] = $documento[0]['montofacturado'];
                $Factura[$i]['nombredoc'] = $documento[0]['nombredoc'];
                $Factura[$i]['iddocumento'] = $documento[0]['iddocumento'];
            }
        }
        $data['Factura'] = $Factura;
        $paginacion = $model->paginadoOrdenVenta($filtro);
        $data['retorno'] = $parametro;
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['totregistros'] = $model->cuentaOrdenVenta($filtro);
        $this->view->show('/facturacion/buscaDuracion.phtml', $data);
    }

    function listaDuracion() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = 1;
        }
        session_start();
        $_SESSION['P_ListaOrden'] = "";
        $model = $this->AutoLoadModel('pdf');
        $ordenventaduracion = $this->AutoLoadModel('ordenventaduracion');

        $Factura = $model->listaOrdenVentaPaginado($id, "");
        for ($i = 0; $i < count($Factura); $i++) {
            $documento = $model->listaFacturaEmitidas($Factura[$i]['idordenventa']);
            $ovd = $ordenventaduracion->listaOrdenVentaDuracionxOrdenVenta($Factura[$i]['idordenventa']);
            $cantidadDuracion = count($ovd);
            for ($y = 0; $y < $cantidadDuracion; $y++) {
                if (strcmp($ovd[$y]['referencia'], "ventas") == 0) {
                    $Factura[$i]['dVentas'] = $ovd[$y]['tiempo'];
                } elseif (strcmp($ovd[$y]['referencia'], "cobranza") == 0) {
                    $Factura[$i]['dCobranza'] = $ovd[$y]['tiempo'];
                } elseif (strcmp($ovd[$y]['referencia'], "almacen") == 0) {
                    $Factura[$i]['dAlmacen'] = $ovd[$y]['tiempo'];
                } elseif (strcmp($ovd[$y]['referencia'], "credito") == 0) {
                    $Factura[$i]['dCredito'] = $ovd[$y]['tiempo'];
                } elseif (strcmp($ovd[$y]['referencia'], "despacho") == 0) {
                    $Factura[$i]['dDespacho'] = $ovd[$y]['tiempo'];
                }
            }
            if (!empty($documento) && count($documento) == 1) {

                $Factura[$i]['serie'] = $documento[0]['serie'];
                $Factura[$i]['numdoc'] = $documento[0]['numdoc'];
                $Factura[$i]['montofacturado'] = $documento[0]['montofacturado'];
                $Factura[$i]['nombredoc'] = $documento[0]['nombredoc'];
                $Factura[$i]['iddocumento'] = $documento[0]['iddocumento'];
            }
        }
        $data['Factura'] = $Factura;
        $paginacion = $model->paginadoOrdenVenta("");
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        //echo '<pre>';
        //print_r($Factura);
        //exit;
        $this->view->show('/facturacion/listadoDuracion.phtml', $data);
    }

    function updateDespacho() {
        $idordenventa = $_REQUEST['idordenventa'];
        $campo = $_REQUEST['campo'];
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $resultado = $ordenventa->updateOrdenventa($idordenventa, $campo);
        echo json_encode($resultado);
    }

    function updateObservacion() {
        $idordenventa = $_REQUEST['idordenventa'];
        $valor = $_REQUEST['valor'];
        $campo = $_REQUEST['campo'];
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $resultado = $ordenventa->UpdateObservacion($idordenventa, $valor, $campo);
        echo json_encode($resultado);
    }

    public function notacreditodevolucion() {
        $this->view->show('/facturacion/notacreditodevolucion.phtml');
    }

    public function generarnotacreditodevolucion() {
        $iddevolucion = $_REQUEST['id'];
        $devolucion = $this->AutoLoadModel('devolucion');
        $dataDevolucion = $devolucion->listaDevolucionxid($iddevolucion);

        if (!empty($dataDevolucion)) {
            $detalledevolucion = $devolucion->listaDetalleDevolucion($iddevolucion, "");
            $totalDevuelto = 0;
            for ($i = 0; $i < count($detalledevolucion); $i++) {
                $totalDevuelto += $detalledevolucion[$i]['importe'];
            }
            $esnotacredito = $devolucion->tieneNotaCredito($iddevolucion);
            if ($esnotacredito == 0) {
                //creamos una nota de credito
                $dataDoc['idRelacionado'] = $dataDevolucion[0]['iddocumento'];
                $dataDoc['electronico'] = $dataDevolucion[0]['electronico'];
                if ($dataDevolucion[0]['electronico'] == 1) {
                    $dataDoc['serie'] = 1;
                    //$dataDoc['numdoc'] = $documento->ultimoCorrelativoElectronico(1, 5);
                }
                $dataDoc['montofacturado'] = round($totalDevuelto, 2);
                $dataDoc['nombredoc'] = 5;
                $dataDoc['idordenventa'] = $dataDevolucion[0]['idordenventa'];
                $dataDoc['fechadoc'] = date('Y-m-d');
                $dataDoc['concepto'] = 1;
                $dataDoc['iddevolucion'] = $iddevolucion;

                $documento = $this->AutoLoadModel('documento');
                $grabaDoc = $documento->grabaDocumento($dataDoc);
                if (!$grabaDoc) {
                    echo 'Error al grabar la nota credito';
                } else {
                    $dataDev['esnotacredito'] = 1;
                    $devolucion->actualizarDevolucion($dataDev, "iddevolucion=" . $iddevolucion);
                }
            }
            $ruta['ruta'] = "/facturacion/notacreditodevolucion";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function buscardocumentoselectronicos() {
        $this->view->show("facturacion/buscardocumentoselectronicos.phtml");
    }

    function buscardocumentoselectronicos_consultar() {
        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : '';
        $txtFechaFin = !empty($_REQUEST['txtFechaFin']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFin'])) : '';
        $filtroSerie = !empty($_REQUEST['filtroSerie']) ? $_REQUEST['filtroSerie'] : '';
        $folioDesde = $_REQUEST['folioDesde'] > 0 ? $_REQUEST['folioDesde'] : 1;
        $folioHasta = $_REQUEST['folioHasta'] > 0 ? $_REQUEST['folioHasta'] : 9999999999;
        $filtroComprobante = !empty($_REQUEST['filtroComprobante']) ? $_REQUEST['filtroComprobante'] : '';

        $arraySerie[1] = 'F';
        $arraySerie[2] = 'B';
        $arraySerie[5] = 'F';
        $arraySerie[6] = 'F';
        $arraySerie[10] = 'P';
        $arraySUNAT[1] = '01';
        $arraySUNAT[2] = '03';
        $arraySUNAT[5] = '07';
        $arraySUNAT[6] = '08';
        $arraySUNAT[10] = '40';
        $arrayElectronicos['01'] = 'Factura Electronica';
        $arrayElectronicos['03'] = 'Boleta Electronica';
        $arrayElectronicos['07'] = 'Nota de Credito Electronica';
        $arrayElectronicos['08'] = 'Nota de Debito Electronica';
        $arrayElectronicos['40'] = 'Comprobante de Percepcion';
        $documento = $this->AutoLoadModel('documento');
        $dataDocumentos = $documento->listaDocumentoElectronico($txtFechaInicio, $txtFechaFin, $filtroSerie, $folioDesde, $folioHasta, $filtroComprobante);
        $tam = count($dataDocumentos);

        for ($i = 0; $i < $tam; $i++) {
            $dataDocumentos[$i]['serie'] = $arraySerie[$dataDocumentos[$i]['nombredoc']] . str_pad($dataDocumentos[$i]['serie'], 3, "0", STR_PAD_LEFT);
            $dataDocumentos[$i]['numdoc'] = str_pad($dataDocumentos[$i]['numdoc'], 8, "0", STR_PAD_LEFT);
            $dataDocumentos[$i]['nombredoc'] = $arraySUNAT[$dataDocumentos[$i]['nombredoc']];
            if (file_exists('suite/20509811858-' . $dataDocumentos[$i]['nombredoc'] . '-' . $dataDocumentos[$i]['serie'] . '-' . $dataDocumentos[$i]['numdoc'] . '.pdf')) {

                echo '<tr>' .
                '<td>' . $arrayElectronicos[$dataDocumentos[$i]['nombredoc']] . '</td>' .
                '<td>' . $dataDocumentos[$i]['serie'] . '</td>' .
                '<td>' . $dataDocumentos[$i]['numdoc'] . '</td>' .
                '<td>' . $dataDocumentos[$i]['codigov'] . '</td>' .
                '<td>' . $dataDocumentos[$i]['razonsocial'] . '</td>' .
                '<td>' . $dataDocumentos[$i]['fechadoc'] . '</td>' .
                '<td>' . $dataDocumentos[$i]['simbolomoneda'] . ' ' . number_format($dataDocumentos[$i]['montofacturado'], 2) . '</td>' .
                '<th><a target="_blank" href="/facturacion/documentoelectronico/20509811858-' . $dataDocumentos[$i]['nombredoc'] . '-' . $dataDocumentos[$i]['serie'] . '-' . $dataDocumentos[$i]['numdoc'] . '.pdf">Ver Documento</a></th>' .
                '</tr>';
            }
        }
    }

    function documentoelectronico() {
        $fichatecnica = "suite/" . $_REQUEST['id'];
        if (file_exists($fichatecnica)) {
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="' . $fichatecnica . '"');
            readfile($fichatecnica);
        } else {
            echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
        }
    }

}

?>