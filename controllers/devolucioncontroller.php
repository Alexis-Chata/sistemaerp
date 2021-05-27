<?php

class devolucioncontroller extends ApplicationGeneral {

    function devolucion() {
        $data['MotivoDevolucion'] = $this->configIniTodo('MotivoDevolucion');
        $devolucion = $this->AutoLoadModel('devolucion');
        $orden = $this->AutoLoadModel('ordenventa');
        $iddevolucion = $_REQUEST['id'];
        if (!empty($iddevolucion)) {
            $condicion = "registrado=0 and estado=1 and aprobado=0 and iddevolucion='" . $iddevolucion . "'";
            $dataDevolucion = $devolucion->listaDevolucionFiltro($condicion);
            if (count($dataDevolucion) > 0) {
                $idordenventa = $dataDevolucion[0]['idordenventa'];
                $dataordenventa = $orden->buscarOrdenVentaxDevoluciones($idordenventa);
                $data['codigov'] = $dataordenventa[0]['codigov'];
                $data['idtxtOV'] = $idordenventa;
                $data['iddevolucion'] = $iddevolucion;
            }
        }
        if (empty($iddevolucion) || count($dataDevolucion) > 0) {
            $this->view->show('/devolucion/devoluciones.phtml', $data);
        } else {
            $ruta['ruta'] = "/devolucion/devolucion";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function listadevoluciones() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $orden = $this->AutoLoadModel('ordenventa');
        $condicion = "estado=1 and registrado=0";
        $data = $devolucion->listaDevolucionFiltro($condicion);
        for ($i = 0; $i < count($data); $i++) {
            $dataOrden = $orden->buscarOrdenVentaxId($data[$i]['idordenventa']);
            $data[$i]['codigov'] = $dataOrden[0]['codigov'];
        }
        $data2['devolucion'] = $data;
        $this->view->show('/devolucion/listadevoluciones.phtml', $data2);
    }

    function detalleDevolucionxOrdenVenta() {
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $orden = $this->AutoLoadModel('ordenventa');
        $devolucion = $this->AutoLoadModel('devolucion');
        $dataDevolucion = $devolucion->ultimaId($idOrdenVenta);
        $iddevolucion = 0;
        if (count($dataDevolucion) > 0) {
            $iddevolucion = $dataDevolucion[0]['iddevolucion'];
            $resp['iddevolucion'] = $dataDevolucion[0]['iddevolucion'];
            $resp['observaciones'] = $dataDevolucion[0]['observaciones'];
            $resp['idmotivodevolucion'] = $dataDevolucion[0]['idmotivodevolucion'];
        } else {
            $resp['iddevolucion'] = '';
            $resp['observaciones'] = '';
            $resp['idmotivodevolucion'] = '';
        }
        $dataDetalle = $devolucion->listaDetalleDevolucionXDetalleOV($idOrdenVenta, $iddevolucion);
        $cantidad = count($dataDetalle);
        $documento = $this->AutoLoadModel('documento');
        $facturaselectronicas = $documento->buscadocumentoxordenventa($idOrdenVenta, "doc.esAnulado!=1 and doc.electronico=1 and doc.nombredoc=1" . (!empty($dataDevolucion[0]['iddocumento']) ? " and doc.iddocumento=" . $dataDevolucion[0]['iddocumento'] : ''));
        $tamFE = count($facturaselectronicas);
        if ($tamFE > 0) {
            $resp['electronico'] = 1;
            $inicio = $facturaselectronicas[0]['desde'] - 1;
            $fin = $facturaselectronicas[0]['hasta'];
            $tope = $fin - $inicio;
            if (!empty($dataDevolucion[0]['iddocumento'])) {
                $resp['editable'] = 1;
                $resp['conDeco'] = "F" . str_pad($facturaselectronicas[0]['serie'], 3, "0", STR_PAD_LEFT) . "-" . str_pad($facturaselectronicas[0]['numdoc'], 8, "0", STR_PAD_LEFT);
                $cantidad = $fin;
            } else {
                $facturas = '';
                $facturasincargar = 0;
                for ($fi = 0; $fi < $tamFE; $fi++) {
                    if ($facturaselectronicas[$fi]['esCargado'] == 1) {
                        $facturas .= '<option value="' . $facturaselectronicas[$fi]['iddocumento'] . '">F' . str_pad($facturaselectronicas[$fi]['serie'], 3, "0", STR_PAD_LEFT) . "-" . str_pad($facturaselectronicas[$fi]['numdoc'], 8, "0", STR_PAD_LEFT) . '</option>';
                    } else {
                        $facturasincargar ++;
                    }
                }
                if (!empty($facturas)) {
                    $resp['editable'] = 0;
                } else {
                    $resp['conDeco'] = $facturasincargar . ' sin cargar.';
                    $resp['editable'] = 1;
                }
            }
            $resp['facturas'] = $facturas;
        } else {
            $inicio = 0;
            $fin = $cantidad;
        }
        $nroLineas = 1;
        $clase = 0;
        $filtro = "idordenventa='$idOrdenVenta'";
        $busqueda = $orden->buscarOrdenxParametro($filtro);
        $simbolo = (($busqueda[0]['IdMoneda'] == 1) ? "S/ " : "US $ ");
        for ($i = $inicio; $i < $cantidad; $i++) {
            $preciodevolucion = (!empty($dataDetalle[$i]['precio']) ? round($dataDetalle[$i]['precio'], 2) : (number_format($dataDetalle[$i]['preciofinal'], 2)));
            $cantidaddevuelta = (!empty($dataDetalle[$i]['cantidad']) ? $dataDetalle[$i]['cantidad'] : 0);
            $columna .= '<tr' . ($i >= $inicio && $i < $fin ? '' : ' style="display: none"') . ($resp['electronico'] == 1 ? ' class="TodasClases C' . $facturaselectronicas[$clase]['iddocumento'] . '"' : '') . '>';
            $columna .=     '<td>' . $nroLineas . '<input class="ColidProducto" type="hidden" value="' . $dataDetalle[$i]['idproducto'] . '"></td>';
            $columna .=     '<td style="text-align: center;">' . $dataDetalle[$i]['codigopa'] . '</td>';
            $columna .=     '<td>' . $dataDetalle[$i]['nompro'] . '</td>';
            $columna .=     '<td style="text-align: center;">' . $dataDetalle[$i]['cantdespacho'] . '</td>';
            $columna .=     '<td style="text-align: center;">' . ($dataDetalle[$i]['cantdespacho'] - $dataDetalle[$i]['cantdevuelta']) . '</td>';
            $columna .=     '<td style="text-align: center;">' . $simbolo . '' . (number_format($dataDetalle[$i]['preciofinal'], 2)) . '</td>';
            if ($cantidaddevuelta > 0) {
                $columna .= '<td style="text-align: right;">' . $simbolo .
                                '<input class="PrecioDevolucion" type="number" readonly value="' . $preciodevolucion . '">' .
                                '</td>' .
                                '<td style="text-align: center;">' .
                                '<a href="#" class="editarPrecio"><img src="/imagenes/editar.gif"></a>' .
                                '</td>' .
                                '<td style="text-align: center;">' .
                                '<a href="#" class="grabarPrecio"><img width="20" height="20" src="/imagenes/grabar.gif"></a>' .
                            '</td>';
            } else {
                $columna .= '<td style="text-align: right;" colspan="3"><b>' . $simbolo . $preciodevolucion . '</b></td>';
            }
            $columna .=     '<td style="text-align: right;"> ' .
                                '<input size="4" id="' . $dataDetalle[$i]['idproducto'] . '" type="number" class="modificar">' .
                            '</td>' .
                            '<td style="text-align: center;">' .
                                '<a href="#" class="save"><img width="20" height="20" src="/imagenes/grabar.gif"></a>' .
                            '</td>';
            $columna .=     '<td style="text-align: center;' . ($cantidaddevuelta > 0 ? ' background: #d2e9fd; font-weight: bold;' : '') . '">' . $cantidaddevuelta . '</td>';
            $importe = $cantidaddevuelta * $preciodevolucion;
            $totalDevuelto += $importe;
            $columna .=     '<td style="text-align: right;">' . $simbolo . ' ' . number_format($importe, 2) . '</td>';
            $columna .= '</tr>';
            if ($nroLineas == $tope) {
                $nroLineas = 0;
                $clase++;
            }
            $nroLineas++;
        }
        $columna .= '<tr>';
        $columna .=     '<td colspan="12" style="text-align: right;background:white;font-size:16px;"><b>TOTAL : </b> </td>';
        $columna .=     '<td style="text-align: right;background:navy;color:white;font-size:16px;">' . $simbolo . ' ' . number_format($totalDevuelto, 2) . '</td>';
        $columna .= '</tr>';
        $resp['columna'] = $columna;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function gestionardevolucion() {
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $iddevolucion = $_REQUEST['iddevolucion'];
        $dataD['observaciones'] = $_REQUEST['observaciones'];
        $dataD['idmotivodevolucion'] = $_REQUEST['lstmotivo'];
        $dataD['idsubmotivodevolucion'] = $_REQUEST['lstsubmotivo'];
        $devolucion = $this->AutoLoadModel('devolucion');
        if (!empty($iddevolucion)) {
            $filtro = "iddevolucion='$iddevolucion' and idordenventa='$idOrdenVenta' and registrado=0 and estado=1 and aprobado=0";
            $devolucion->actualizarDevolucion($dataD, $filtro);
            $resp['iddevolucion'] = $iddevolucion;
        } else {
            $dataD['idordenventa'] = $idOrdenVenta;
            $dataD['estado'] = 1;
            $resp['iddevolucion'] = $devolucion->grabaDevolucion($dataD);
        }
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function listadevolucionesAprobadas() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $orden = $this->AutoLoadModel('ordenventa');
        $condicion = "estado=1 and registrado=1 and aprobado=0";
        $data = $devolucion->listaDevolucionFiltro($condicion);
        for ($i = 0; $i < count($data); $i++) {
            $dataOrden = $orden->buscarOrdenVentaxId($data[$i]['idordenventa']);
            $data[$i]['codigov'] = $dataOrden[0]['codigov'];
            $data[$i]['simbolo'] = $dataOrden[0]['Simbolo'];
        }
        $data2['devolucion'] = $data;
        $this->view->show('/devolucion/listadevolucionesaprobadas.phtml', $data2);
    }

    function grabaDetalle() {
        $detalle = $this->AutoLoadModel('detalleordenventa');
        $orden = $this->AutoLoadModel('ordenventa');
        $devolucion = $this->AutoLoadModel('devolucion');
        $claves = array();
        $columna = "";
        $iddevolucion = "";
        $cantidadDevuelta = array();
        $dataDevolucion = "";
        //recibimos codigo (codigov) y idDevolucion
        $iddevolucion = $_REQUEST['idNDevolucion'];
        $codigov = $_REQUEST['idOV'];
        //recuramos el id orden venta 
        $filtro = "codigov='$codigov'";
        $busqueda = $orden->buscarOrdenxParametro($filtro);
        $id = $busqueda[0]['idordenventa'];
        $IdMoneda = $busqueda[0]['IdMoneda'];
        $simbolo = (($IdMoneda == 1) ? "S/" : "US $");
        //verificamos si esta aprobado
        $verificacion = $devolucion->verificar($iddevolucion);
        $resp['electronico'] = 0;
        if (!empty($busqueda)) {
            if (empty($verificacion)) {
                //buscamos los detalles de la orden de venta
                $dataDetalle = $detalle->listaDetalleOrdenVenta($id);
                if (!empty($id) && !empty($iddevolucion)) {
                    //verificamos si existe la devolucion
                    $dataDevolucion = $devolucion->listaDevolucion2($id, $iddevolucion);
                }
                //verificamos si existe la orden de venta
                $ExiteOrden = $orden->buscarOrdenVAprobadoPorAlmacen($id);
                if (count($ExiteOrden) != 0 && count($dataDevolucion) != 0) {
                    if (empty($iddevolucion)) {
                        //verificamos que haya una sola orden de venta en devoluciones que no este confirmada cuando
                        $condicion = "estado=1 and registrado=0 and aprobado=0 and idordenventa='$id'";
                        $cantidadExitencia = $devolucion->listaDevolucionFiltro($condicion);
                        if (count($cantidadExitencia) > 0) {
                            $columna = '<tr><td colspan="7" align="center">Ya existe una devolucion que no ha sido confirmada</td></tr>';
                            $resp['columna'] = $columna;
                            header('Content-type: application/json; charset=utf-8');
                            echo json_encode($resp);
                            exit;
                        } else {
                            $dataD['idordenventa'] = $id;
                            $dataD['estado'] = 1;
                            $exito1 = $devolucion->grabaDevolucion($dataD);
                        }
                        //agregamos cada detalle de la orden de venta a la tabla detalledevolucion
                        //con valores vacios en caso de  las cantidades
                        for ($i = 0; $i < count($dataDetalle); $i++) {
                            $dataDet['iddevolucion'] = $exito1;
                            $dataDet['idproducto'] = $dataDetalle[$i]['idproducto'];
                            $dataDet['precio'] = $dataDetalle[$i]['preciofinal'];
                            $dataDet['cantidad'] = 0;
                            $dataDet['importe'] = number_format(0, 2);
                            $dataDet['estado'] = 1;
                            $PrecioDevolucion[$i] = $dataDetalle[$i]['preciofinal'];
                            $exitoG = $devolucion->grabaDetalleDevolucion($dataDet);
                            $claves[$i] = $exitoG;
                        }
                    } else {
                        //obtenemos el id de devolucion
                        $iddevolucion = $dataDevolucion[0]['iddevolucion'];
                        $datosDetalleDevolucion = $devolucion->listaDetalleDevolucion($iddevolucion, "");
                        for ($i = 0; $i < count($datosDetalleDevolucion); $i++) {
                            $claves[$i] = $datosDetalleDevolucion[$i]['iddetalledevolucion'];
                            $cantidadDevuelta[$i] = $datosDetalleDevolucion[$i]['cantidad'];
                            $PrecioDevolucion[$i] = $datosDetalleDevolucion[$i]['precio'];
                        }
                    }
                    $documento = $this->AutoLoadModel('documento');
                    $facturaselectronicas = $documento->buscadocumentoxordenventa($id, "doc.esAnulado!=1 and doc.electronico=1 and doc.nombredoc=1" . (!empty($dataDevolucion[0]['iddocumento']) ? " and doc.iddocumento=" . $dataDevolucion[0]['iddocumento'] : ''));
                    $cantidad = count($dataDetalle);
                    if (count($facturaselectronicas) > 0) {
                        $resp['electronico'] = 1;
                        $inicio = $facturaselectronicas[0]['desde'] - 1;
                        $fin = $facturaselectronicas[0]['hasta'];
                        $tope = $fin - $inicio;
                        if (!empty($dataDevolucion[0]['iddocumento'])) {
                            $resp['editable'] = 1;
                            $resp['conDeco'] = "F" . str_pad($facturaselectronicas[0]['serie'], 3, "0", STR_PAD_LEFT) . "-" . str_pad($facturaselectronicas[0]['numdoc'], 8, "0", STR_PAD_LEFT);
                        } else {
                            $resp['editable'] = 0;
                            $facturas = '';
                            for ($fi = 0; $fi < count($facturaselectronicas); $fi++) {
                                $facturas .= '<option value="' . ($facturaselectronicas[$fi]['esCargado'] == 0 ? 0 : $facturaselectronicas[$fi]['iddocumento']) . '">F' . str_pad($facturaselectronicas[$fi]['serie'], 3, "0", STR_PAD_LEFT) . "-" . str_pad($facturaselectronicas[$fi]['numdoc'], 8, "0", STR_PAD_LEFT) . '</option>';
                            }
                        }
                        $resp['facturas'] = $facturas;
                    } else {
                        $inicio = 0;
                        $fin = $cantidad;
                    }
                    //recuperamos el iddetalledevolucion para mandar a la vista
                    $nroLineas = 1;
                    $clase = 0;
                    for ($i = 0; $i < $cantidad; $i++) {
                        $columna .= '<tr' . ($i >= $inicio && $i < $fin ? '' : ' style="display: none"') . ($resp['electronico'] == 1 ? ' class="TodasClases C' . $facturaselectronicas[$clase]['iddocumento'] . '"' : '') . '>';
                        $columna .=     '<td>' . $nroLineas . '<input class="idDetalleDevolucion" type="hidden" value="' . $claves[$i] . '"></td>';
                        $columna .=     '<td style="text-align: center;">' . $dataDetalle[$i]['codigopa'] . '</td>';
                        $columna .=     '<td>' . $dataDetalle[$i]['nompro'] . '</td>';
                        $columna .=     '<td style="text-align: center;">' . $dataDetalle[$i]['cantdespacho'] . '</td>';
                        $columna .=     '<td style="text-align: center;">' . ($dataDetalle[$i]['cantdespacho'] - $dataDetalle[$i]['cantdevuelta']) . '</td>';
                        $columna .=     '<td style="text-align: center;">' . $simbolo . '' . (number_format($dataDetalle[$i]['preciofinal'], 2)) . '</td>';
                        $columna .=     '<td style="text-align: right;">' . $simbolo . '
                                            <input size="6"  class="PrecioDevolucion" type="text" readonly value="' . round($PrecioDevolucion[$i], 2) . '">
                                            <a href="#" class="editarPrecio" ><img src="/imagenes/editar.gif"></a>
                                            <a href="#" class="grabarPrecio" ><img width="20" height="20" src="/imagenes/grabar.gif"></a>
					</td>';
                        $columna .=     '<td><input style="margin:auto;display:block;float:left;margin-right:10px" size="4" id="' . $claves[$i] . '" type="text"  class="modificar" ';
                        $columna .=     '"> <a href="#" class="save"><img width="20" height="20" src="/imagenes/grabar.gif"></a> </td>';
                        $columna .=     '<td style="text-align: center;"><input readonly style="border:none;width:65px;" class="cantidadDevuelta" type="text" value="' . $cantidadDevuelta[$i] . '"></td>';
                        $importe = $cantidadDevuelta[$i] * $PrecioDevolucion[$i];
                        $totalDevuelto += $importe;
                        $columna .=     '<td style="text-align: right;">' . $simbolo . ' ' . number_format($importe, 2) . '</td>';
                        $columna .= '</tr>';
                        if ($nroLineas == $tope) {
                            $nroLineas = 0;
                            $clase++;
                        }
                        $nroLineas++;
                    }
                    $columna .= '<tr>';
                    $columna .=     '<td colspan="9" style="text-align: right;background:white;font-size:15px;"><b>TOTAL : </b> </td>';
                    $columna .=     '<td style="text-align: right;background:green;color:white;font-size:15px;">' . $simbolo . ' ' . number_format($totalDevuelto, 2) . '</td>';
                    $columna .= '</tr>';
                    //echo  $columna;
                } else {
                    //$columna='<tr><td colspan="7">Existe orden: '.$id.'     devolucion:  '.$iddevolucion.'</td></tr>';
                    $columna = '<tr><td colspan="7">No Existe esa Orden de Venta o N° de Devolucion</td></tr>';
                    //echo $columna;
                }
            } else {
                $columna = '<tr><td colspan="7">Esta Aprobado esta Devolucion</td></tr>';
                //echo $columna;
            }
        } else {
            $columna = '<tr><td colspan="7">Error al Ingresar Orden Venta</td></tr>';
            //echo $columna;
        }
        $resp['columna'] = $columna;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function actualizaDevolucion() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $detalleordenventa = $this->AutoLoadModel('detalleordenventa');
        $idordenventa = $_REQUEST['idordenventa'];
        $iddevolucion = $_REQUEST['iddevolucion'];
        $idproducto = $_REQUEST['idProducto'];
        $cantidad = $_REQUEST['cantidad'];
        $total = 0;
        $DOV = $detalleordenventa->listaDetalleOrdenVentaxProducto($idordenventa, $idproducto);
        $cantidadMaxima = $DOV[0]['cantdespacho'] - $DOV[0]['cantdevuelta'];
        $dataDDev = $devolucion->verificarDetalleDevolucion($iddevolucion, $idproducto);
        $existeDDev = count($dataDDev);
        if ($existeDDev > 0) {
            if ($dataDDev[0]['estado'] == 1) {
                $cantidad += $dataDDev[0]['cantidad'];
            }
        }
        if ($cantidad < 0) {
            $resp['msj'] = 'Sobrepaso el Minimo';
        } elseif ($cantidad > $cantidadMaxima) {
            $resp['msj'] = 'Sobrepaso el Maximo';
        } else {
            $NuevadataDDev['estado'] = 1;
            $NuevadataDDev['cantidad'] = $cantidad;
            if ($existeDDev > 0) {
                if ($dataDDev[0]['estado'] == 0 || $dataDDev[0]['cantidad'] == 0) {
                    $NuevadataDDev['precio'] = $DOV[0]['preciofinal'];
                    $NuevadataDDev['importe'] = $DOV[0]['preciofinal'] * $cantidad;
                } else {
                    $NuevadataDDev['importe'] = $dataDDev[0]['precio'] * $cantidad;
                }
                $exito = $devolucion->actualizaDetalleDevolucion($NuevadataDDev, $iddevolucion, $dataDDev[0]['iddetalledevolucion']);
            } else {
                $NuevadataDDev['precio'] = $DOV[0]['preciofinal'];
                $NuevadataDDev['importe'] = $DOV[0]['preciofinal'] * $cantidad;
                $NuevadataDDev['iddevolucion'] = $iddevolucion;
                $NuevadataDDev['idproducto'] = $idproducto;
                $exito = $devolucion->grabaDetalleDevolucion($NuevadataDDev);
            }
            if ($exito) {
                $data2 = $devolucion->listaDetalleDevolucion($iddevolucion, "");
                for ($i = 0; $i < count($data2); $i++) {
                    $total += $data2[$i]['importe'];
                }
                $data3['importetotal'] = $total;
                $filtro = "iddevolucion='$iddevolucion'";
                $exito2 = $devolucion->actualizarDevolucion($data3, $filtro);
            }
            if ($exito2) {
                $resp['msj'] = 'Aprobado';
            } else {
                $resp['msj'] = '¡Error!';
            }
        }
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function cambiaPrecioDevolucion() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $detalleordenventa = $this->AutoLoadModel('detalleordenventa');
        $idordenventa = $_REQUEST['idordenventa'];
        $iddevolucion = $_REQUEST['iddevolucion'];
        $idproducto = $_REQUEST['idProducto'];
        $precio = round($_REQUEST['precio'], 2);
        if ($precio > 0) {
            $total = 0;
            $dataDDev = $devolucion->verificarDetalleDevolucion($iddevolucion, $idproducto);
            $existeDDev = count($dataDDev);
            $exito = false;
            if ($existeDDev > 0) {
                if ($dataDDev[0]['estado'] == 1) {
                    $NuevadataDDev['precio'] = $precio;
                    $NuevadataDDev['importe'] = $precio * $dataDDev[0]['cantidad'];
                    $exito = $devolucion->actualizaDetalleDevolucion($NuevadataDDev, $iddevolucion, $dataDDev[0]['iddetalledevolucion']);
                }
            } else {
                $resp['msj'] = 'El producto no tiene cantidad devuelta registrada.';
            }
            if ($exito) {
                $data2 = $devolucion->listaDetalleDevolucion($iddevolucion, "");
                for ($i = 0; $i < count($data2); $i++) {
                    $total += $data2[$i]['importe'];
                }
                $data3['importetotal'] = $total;
                $filtro = "iddevolucion='$iddevolucion'";
                $exito2 = $devolucion->actualizarDevolucion($data3, $filtro);
                if ($exito2) {
                    $resp['msj'] = 'Aprobado';
                } else {
                    $resp['msj'] = '¡Error!';
                }
            }
        } else {
            $resp['msj'] = 'El Precio ingresado debe ser mayor que cero (0).';
        }
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function obtieneIdDevolucion() {
        $idOV = $_REQUEST['idOV'];
        $devolucion = $this->AutoLoadModel('devolucion');
        $id = $devolucion->nuevoId();
        echo $id;
    }

    function obtieneDatosOV() {
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $devolucion = $this->AutoLoadModel('devolucion');
        $cliente = $this->AutoLoadModel('cliente');
        $dataCliente = $cliente->buscarClienteOrdenVenta($idOrdenVenta);
        if (count($dataCliente) > 0) {
            $resp['razonsoscial'] = $dataCliente[0]['razonsocial'];
            $resp['ruc'] = $dataCliente[0]['ruc'];
        } else {
            $resp['razonsoscial'] = '---';
            $resp['ruc'] = '---';
        }
        $dataDevolucion = $devolucion->ultimaId($idOrdenVenta);
        $iddevolucion = 0;
        $tamanio = 0;
        $temporalmotivo = '<option value="0">Seleccione una opcion</option>';
        if (count($dataDevolucion) > 0) {
            $iddevolucion = $dataDevolucion[0]['iddevolucion'];
            $resp['iddevolucion'] = $dataDevolucion[0]['iddevolucion'];
            $resp['observaciones'] = $dataDevolucion[0]['observaciones'];
            $resp['idmotivodevolucion'] = $dataDevolucion[0]['idmotivodevolucion'];
            $resp['idsubmotivodevolucion'] = $dataDevolucion[0]['idsubmotivodevolucion'];
            $submotivodevolucionmodel = $this->AutoLoadModel('submotivodevolucion');
            $datasubmotivo = $submotivodevolucionmodel->leerPorTipo($dataDevolucion[0]['idmotivodevolucion']);
            $tamanio = count($datasubmotivo);
            for ($i = 0; $i < $tamanio; $i++) {
                $temporalmotivo .= '<option value="' . $datasubmotivo[$i]['idsubmotivodevolucion'] . '">' . $datasubmotivo[$i]['descripcion'] . '</option>';
            }
        } else {
            $resp['iddevolucion'] = '';
            $resp['observaciones'] = '';
            $resp['idmotivodevolucion'] = '';
            $resp['idsubmotivodevolucion'] = '';
        }
        $resp['motivos'] = $temporalmotivo;
        $resp['tamanio'] = $tamanio;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function grabaAprobacion() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $idordenventa = $_REQUEST['idordenventa'];
        $iddevolucion = $_REQUEST['iddevolucion'];
        $data2 = $devolucion->listaDetalleDevolucion($iddevolucion, "");
        if (count($data2) > 0) {
            $data['registrado'] = 1;
            $data['fecharegistrada'] = date('Y/m/d H:i:s');
            $filtro = "iddevolucion='$iddevolucion' and idordenventa='$idordenventa' and registrado!='1'";
            $exito = $devolucion->actualizarDevolucion($data, $filtro);
            if ($exito) {
                $resp['msj'] = 'Aprobado';
            } else {
                $resp['msj'] = '¡Error!';
            }
        } else {
            $resp['msj'] = 'La devolucion esta vacia.';
        }
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function listaDetalleDevolucion() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $producto = $this->AutoLoadModel('producto');
        $iddevolucion = $_REQUEST['IDD'];
        $columna = '';
        $dataDevolucion = $devolucion->listaDevolucionxid($iddevolucion);
        $dataDetalle = $devolucion->listaDetalleDevolucion($iddevolucion, "");
        $idOV = $dataDevolucion[0]['idordenventa'];
        $OBJ_OrdenVenta = $this->AutoLoadModel('OrdenVenta');
        $dataOrdenVenta = $OBJ_OrdenVenta->buscarOrdenVentaxId($idOV);
        $simbolomoneda = $dataOrdenVenta[0]['Simbolo'];
        for ($i = 0; $i < count($dataDetalle); $i++) {
            $dataProducto = $producto->buscaProductoOrdenCompra($dataDetalle[$i]['idproducto']);
            if ($dataDetalle[$i]['cantidad'] > 0) {
                $columna .= '<tr>';
                $columna .=     '<td style="text-align: center;">' . ($i + 1) . '</td>';
                $columna .=     '<td style="text-align: center;">' . $dataProducto[0]['codigopa'] . '</td>';
                $columna .=     '<td>' . $dataProducto[0]['nompro'] . '</td>';
                $columna .=     '<td style="text-align: right;">' . $simbolomoneda . ' ' . number_format($dataDetalle[$i]['precio'], 2) . '</td>';
                $columna .=     '<td style="text-align: center;">' . $dataDetalle[$i]['cantidad'] . '</td>';
                $columna .=     '<td style="text-align: right;">' . $simbolomoneda . ' ' . number_format($dataDetalle[$i]['importe'], 2) . '</td>';
                $columna .= '</tr>';
            }
        }
        $columna .= '<tr><td colspan="4"></td><td style="text-align: center;">Total</td><td style="text-align: right;">' . $simbolomoneda . ' ' . number_format($dataDevolucion[0]['importetotal'], 2) . '</td></tr>';
        echo $columna;
    }

    function encabezadoDevolucion() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $iddevolucion = $_REQUEST['IDD'];
        $dataDevolucion = $devolucion->listaDevolucionxid($iddevolucion);
        $dataCliente = $devolucion->listaOrdenconCliente($dataDevolucion[0]['idordenventa']);
        $columna .= '<thead><tr>';
        $columna .=     '<th colspan="3">Impresion de Devoluciones</th><th>Observaciones :</th><td colspan="3">' . $dataDevolucion[0]['observaciones'] . '</td>';
        $columna .=     '</tr><tr>';
        $columna .=     '<th>Orden Venta</th>';
        $columna .=     '<td>' . $dataCliente[0]['codigov'] . '</td>';
        $columna .=     '<th>Fecha y Hora de Impresion</th>';
        $columna .=     '<td>' . date('d-m-Y H:j:s') . '</td>';
        $columna .=     '<th>Situacion</th>';
        $columna .=     '<td>' . ($dataCliente[0]['situacion'] == '' ? 'Pendiente' : $dataCliente[0]['situacion']) . '</td>';
        $columna .=     '</tr><tr>';
        $columna .=     '<th>Razon Social</th>';
        $columna .=     '<td>' . $dataCliente[0]['razonsocial'] . '</td>';
        $columna .=     '<th>RUC</th>';
        $columna .=     '<td>' . $dataCliente[0]['ruc'] . '</td>';
        $columna .=     '<th>Fecha de Devolucion</th>';
        $columna .=     '<td>' . date('d-m-Y H:j:s', strtotime($dataDevolucion[0]['fechaaprobada'])) . '</td>';
        $columna .=     '</tr><tr>';
        $columna .= '</tr></thead>';
        echo $columna;
    }

    function eliminarDevolucion() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $iddevolucion = $_REQUEST['id'];
        $condicion = "registrado=0 and estado=1 and aprobado=0 and iddevolucion='" . $iddevolucion . "'";
        $dataDevolucion = $devolucion->listaDevolucionFiltro($condicion);
        if (count($dataDevolucion) > 0) {
            $exito = $devolucion->eliminarDevolucion($iddevolucion);
            if ($exito) {
                $exito2 = $devolucion->eliminarDetalleDevolucion($iddevolucion);
            }
        }
        $ruta['ruta'] = "/devolucion/listadevoluciones";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function cambiarSubMotivoDevolucion() {
        $submotivodevolucion = $_REQUEST['idmotivodevolucion'];
        $tamanio = 0;
        $temporalmotivo = '<option value="0">Seleccione una opcion</option>';
        if ($submotivodevolucion > 0) {
            $submotivodevolucionmodel = $this->AutoLoadModel('submotivodevolucion');
            $datasubmotivo = $submotivodevolucionmodel->leerPorTipo($submotivodevolucion);
            $tamanio = count($datasubmotivo);
            for ($i = 0; $i < $tamanio; $i++) {
                $temporalmotivo .= '<option value="' . $datasubmotivo[$i]['idsubmotivodevolucion'] . '">' . $datasubmotivo[$i]['descripcion'] . '</option>';
            }
        }
        $resp['motivos'] = $temporalmotivo;
        $resp['tamanio'] = $tamanio;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function eliminarDevolucionesAprobadas() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $iddevolucion = $_REQUEST['id'];
        $condicion = "registrado=1 and estado=1 and aprobado=0 and iddevolucion='" . $iddevolucion . "'";
        $dataDevolucion = $devolucion->listaDevolucionFiltro($condicion);
        if (count($dataDevolucion) > 0) {
            $exito = $devolucion->eliminarDevolucion($iddevolucion);
            if ($exito) {
                $exito2 = $devolucion->eliminarDetalleDevolucion($iddevolucion);
            }
        }
        $ruta['ruta'] = "/devolucion/listadevolucionesAprobadas";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function grabaconfirmarPedido() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $iddevolucion = $_REQUEST['iddevolucion'];
        $condicion = "registrado=1 and estado=1 and aprobado=0 and iddevolucion='" . $iddevolucion . "'";
        $dataDevolucion = $devolucion->listaDevolucionFiltro($condicion);
        $msj = "";
        if (!empty($dataDevolucion)) {
            $totalDevuelto = 0;
            $exito = $devolucion->confirmar($iddevolucion);
            $movimiento = $this->AutoLoadModel('movimiento');
            $idordenventa = $dataDevolucion[0]['idordenventa'];
            $filtroMovimiento = "iddevolucion='$iddevolucion' and idordenventa='$idordenventa'";
            $banderaMovimiento = $movimiento->buscaMovimientoxfiltro($filtroMovimiento);
            if (empty($banderaMovimiento)) {
                $producto = $this->AutoLoadModel('producto');
                $orden = $this->AutoLoadModel('ordenventa');
                $detMovimiento = $this->AutoLoadModel('detallemovimiento');
                $detalleordenventa = $this->AutoLoadModel('detalleordenventa');
                $documento = $this->AutoLoadModel('documento');
                $filtro = " doc.nombredoc=1";
                $dataBusqueda = $documento->buscadocumentoxordenventaPrimero($idordenventa, $filtro);
                if (!empty($dataBusqueda)) {
                    $dataMovimiento['tipodoc'] = 5;
                    $dataMovimiento['iddocumentotipo'] = 5;
                    $dataMovimiento['essunat'] = 1;
                }
                $dataMovimiento['conceptomovimiento'] = 3;
                $dataMovimiento['tipomovimiento'] = 1;
                $dataMovimiento['idtipooperacion'] = 5;
                $dataMovimiento['idordenventa'] = $idordenventa;
                $dataMovimiento['iddevolucion'] = $iddevolucion;
                $dataMovimiento['fechamovimiento'] = date('Y-m-d H:j:s');
                $exitoM = $movimiento->grabaMovimiento($dataMovimiento);
                $detalledevolucion = $devolucion->listaDetalleDevolucion($iddevolucion, "");
                $totalDevuelto = 0;
                for ($i = 0; $i < count($detalledevolucion); $i++) {
                    $dataProducto = $producto->buscaProducto($detalledevolucion[$i]['idproducto']);
                    //actualizamos el stockactual de producto
                    $totalDevuelto += $detalledevolucion[$i]['importe'];
                    $data['stockactual'] = $detalledevolucion[$i]['cantidad'] + $dataProducto[0]['stockactual'];
                    $data['stockdisponible'] = $detalledevolucion[$i]['cantidad'] + $dataProducto[0]['stockdisponible'];
                    $data['esagotado'] = 0;
                    $data['fechaagotado'] = '';
                    $exito2 = $producto->actualizaProducto($data, $detalledevolucion[$i]['idproducto']);
                    $dataDetalleMovimiento['cantidad'] = $detalledevolucion[$i]['cantidad'];
                    $dataDetalleMovimiento['idmovimiento'] = $exitoM;
                    $dataDetalleMovimiento['pu'] = $detalledevolucion[$i]['precio'];
                    $dataDetalleMovimiento['preciovalorizado'] = $dataProducto[0]['preciocosto'];
                    $dataDetalleMovimiento['idproducto'] = $detalledevolucion[$i]['idproducto'];
                    $dataDetalleMovimiento['stockactual'] = $dataProducto[0]['stockactual'] + $detalledevolucion[$i]['cantidad'];
                    $dataDetalleMovimiento['stockdisponibledm'] = $detalledevolucion[$i]['cantidad'] + $detalledevolucion[$i]['cantidad'];
                    $dataDetalleMovimiento['importe'] = $detalledevolucion[$i]['precio'] * $detalledevolucion[$i]['cantidad'];
                    $exitoDM = $detMovimiento->grabaDetalleMovimieto($dataDetalleMovimiento);
                    $dataP = $detalleordenventa->listaDetalleOrdenVentaxProducto($idordenventa, $detalledevolucion[$i]['idproducto']);
                    //actualiza la cantidad devuelta necesito idordenventa y idproducto
                    $filtro = "idordenventa='$idordenventa' and idproducto='" . $detalledevolucion[$i]['idproducto'] . "'";
                    $dataDOV['cantdevuelta'] = $dataP[0]['cantdevuelta'] + $detalledevolucion[$i]['cantidad'];
                    $exito4 = $detalleordenventa->actualizaxFiltro($dataDOV, $filtro);
                }
                $dataFinal['importedevolucion'] = $devolucion->totalDevolucionsAprobadas($idordenventa);
                $exito3 = $orden->actualizaOrdenVenta($dataFinal, $idordenventa);
                if (count($dataBusqueda) > 0 && $totalDevuelto > 0) {
                    //creamos una nota de credito
                    $dataDoc['idRelacionado'] = $dataDevolucion[0]['iddocumento'];
                    $dataDoc['electronico'] = $dataDevolucion[0]['electronico'];
                    if ($dataDevolucion[0]['electronico'] == 1) {
                        $dataDoc['serie'] = 1;
                    }
                    $dataDoc['montofacturado'] = round($totalDevuelto, 2);
                    $dataDoc['nombredoc'] = 5;
                    $dataDoc['idordenventa'] = $idordenventa;
                    $dataDoc['fechadoc'] = date('Y-m-d');
                    $dataDoc['concepto'] = 1;
                    $dataDoc['iddevolucion'] = $iddevolucion;
                    $grabaDoc = $documento->grabaDocumento($dataDoc);
                    if (!$grabaDoc) {
                        $msj = 'Error al grabar la nota credito.';
                    }
                    $dataIngreso['tipocobro'] = 10;
                } else {
                    $dataIngreso['tipocobro'] = 7;
                }
                if (empty($msj)) {
                    $dataordenventa = $orden->buscarOrdenVentaxId($idordenventa);
                    $ingresos = $this->AutoLoadModel('ingresos');
                    $dataIngreso['idOrdenVenta'] = $idordenventa;
                    $dataIngreso['idcobrador'] = 398;
                    $dataIngreso['montoingresado'] = round($totalDevuelto, 2);
                    $dataIngreso['saldo'] = round($totalDevuelto, 2);
                    $dataIngreso['fcobro'] = date('Y-m-d');
                    $dataIngreso['idcliente'] = $dataordenventa[0]['idcliente'];
                    $grabaIngreso = $ingresos->graba($dataIngreso);
                    if ($grabaIngreso) {
                        $msj = 'ok';
                    } else {
                        $msj = 'Error al grabar el ingreso.';
                    }
                }
            } else {
                $msj = 'El movimiento ya ha sido registrado.';
            }
        } else {
            $msj = 'Tu devolucion ya ha sido aprobada.';
        }
        $resp['msj'] = $msj;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function desaprobarDevolucion() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $iddevolucion = $_REQUEST['id'];
        $condicion = "registrado=1 and estado=1 and aprobado=0 and iddevolucion='" . $iddevolucion . "'";
        $dataDevolucion = $devolucion->listaDevolucionFiltro($condicion);
        if (count($dataDevolucion) > 0) {
            $data['registrado'] = 0;
            $data['fecharegistrada'] = "";
            $data['aprobado'] = 0;
            $data['fechaaprobada'] = "";
            $filtro = "iddevolucion='$iddevolucion'";
            $exito = $devolucion->actualizarDevolucion($data, $filtro);
        }
        $ruta['ruta'] = "/devolucion/listadevolucionesAprobadas";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function confirmarPedido() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $iddevolucion = $_REQUEST['id'];
        $datos['documento'] = $this->tipoDocumento();
        $dataDevolucion = $devolucion->listaDevolucionxid($iddevolucion);
        $datos['iddevolucion'] = $iddevolucion;
        $datos['codigov'] = 'OV-' . date('y') . str_pad($dataDevolucion[0]['idordenventa'], 6, '0', STR_PAD_LEFT);
        $datos['idordenventa'] = $dataDevolucion[0]['idordenventa'];
        $this->view->show('/devolucion/confirmar.phtml', $datos);
    }

    function listarDevolucionTotal() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $orden = $this->AutoLoadModel('ordenventa');
        $contador = 0;
        $data = array();
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        session_start();
        $_SESSION['P_Devolucion'] = "";
        $dataDevolucion = $devolucion->listaDevolucionesPaginado("", $pagina);
        $cantidadDevoluciones = count($dataDevolucion);
        for ($i = 0; $i < $cantidadDevoluciones; $i++) {
            $idordenventa = $dataDevolucion[$i]['idordenventa'];
            $iddevolucion = $dataDevolucion[$i]['iddevolucion'];
            $filtro = "estado=1 and idordenventa='$idordenventa'";
            $dataOrden = $orden->buscarOrdenxParametro($filtro);
            $consultaCorrelativos = $devolucion->consultaCorrelativos($idordenventa);
            foreach ($consultaCorrelativos as $v) {
                if ($v["electronico"] == '1') {
                    if ($v["nombredoc"] == '1') {
                        $dataDevolucion[$i]['comprobante'] = "Factura Electronica";
                        $serieComprobante = "F001";
                        $correlativoComprobante = $devolucion->add_ceros($v["numdoc"], 8);
                    }
                    if ($v["nombredoc"] == '2') {
                        $dataDevolucion[$i]['comprobante'] = "Boleta Electronica";
                        $serieComprobante = "B001";
                        $correlativoComprobante = $devolucion->add_ceros($v["numdoc"], 8);
                    }
                }
                if ($v["electronico"] == '0') {
                    if ($v["nombredoc"] == '1') {
                        $dataDevolucion[$i]['comprobante'] = "Factura Fisica";
                        $serieComprobante = $v["serie"];
                        $correlativoComprobante = $v["numdoc"];
                    }
                    if ($v["nombredoc"] == '2') {
                        $dataDevolucion[$i]['comprobante'] = "Boleta Fisica";
                        $serieComprobante = $v["serie"];
                        $correlativoComprobante = $v["numdoc"];
                    }
                    if ($dataOrden[0]['esfacturado'] == 1) {
                        $imagen = "facturar.png";
                    }
                }
                if ($v["nombredoc"] == '4') {
                    $dataDevolucion[$i]['comprobante'] = "Guia Remision";
                    $serieComprobante = $devolucion->add_ceros($v["serie"], 3);
                    $correlativoComprobante = $v["numdoc"];
                }
                if ($dataOrden[0]['esfacturado'] == 0) {
                    if ($v["electronico"] == '1') {
                        $dataDevolucion[$i]['comprobante'] = "-";
                        $serieComprobante = '';
                        $correlativoComprobante = '';
                    }
                }
            }
            $dataDevolucion[$i]['simbolo'] = ($dataOrden[0]['IdMoneda'] == 1) ? "S/" : "US $";
            $dataDevolucion[$i]['icono'] = $dataOrden[0]['esfacturado'] == 1 ? '<img width="20" high="20" src="/imagenes/' . ($dataDevolucion[$i]['electronico'] == 1 ? 'facturarele.png' : 'facturar.png') . '">' : '-';
            $dataDevolucion[$i]['esfacturado'] = $serieComprobante . ' - ' . $correlativoComprobante;
            $dataDevolucion[$i]['codigov'] = $dataOrden[0]['codigov'];
            $serieComprobante = '';
            $correlativoComprobante = '';
        }
        $paginacion = $devolucion->paginadoDevoluciones("");
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['dataDevolucion'] = $dataDevolucion;
        $this->view->show('/devolucion/listadevolucionestotales.phtml', $data);
    }

    function buscaDevoluciones() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $orden = $this->AutoLoadModel('ordenventa');
        $contador = 0;
        $data = array();
        $pagina = $_GET['id'];
        if (empty($_GET['id'])) {
            $pagina = 1;
        }
        session_start();
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_Devolucion'] = $_REQUEST['txtBusqueda'];
        }
        $parametro = $_SESSION['P_Devolucion'];
        $dataDevolucion = $devolucion->listaDevolucionesPaginado("", $pagina, $parametro);
        $cantidadDevoluciones = count($dataDevolucion);
        for ($i = 0; $i < $cantidadDevoluciones; $i++) {
            $idordenventa = $dataDevolucion[$i]['idordenventa'];
            $iddevolucion = $dataDevolucion[$i]['iddevolucion'];
            $filtro = "estado=1 and idordenventa='$idordenventa'";
            $dataOrden = $orden->buscarOrdenxParametro($filtro);
            $consultaCorrelativos = $devolucion->consultaCorrelativos($idordenventa);
            foreach ($consultaCorrelativos as $v) {
                if ($v["electronico"] == '1') {
                    if ($v["nombredoc"] == '1') {
                        $dataDevolucion[$i]['comprobante'] = "Factura Electronica";
                        $serieComprobante = "F001";
                        $correlativoComprobante = $devolucion->add_ceros($v["numdoc"], 8);
                    }
                    if ($v["nombredoc"] == '2') {
                        $dataDevolucion[$i]['comprobante'] = "Boleta Electronica";
                        $serieComprobante = "B001";
                        $correlativoComprobante = $devolucion->add_ceros($v["numdoc"], 8);
                    }
                }
                if ($v["electronico"] == '0') {
                    if ($v["nombredoc"] == '1') {
                        $dataDevolucion[$i]['comprobante'] = "Factura Fisica";
                        $serieComprobante = $v["serie"];
                        $correlativoComprobante = $v["numdoc"];
                    }
                    if ($v["nombredoc"] == '2') {
                        $dataDevolucion[$i]['comprobante'] = "Boleta Fisica";
                        $serieComprobante = $v["serie"];
                        $correlativoComprobante = $v["numdoc"];
                    }
                    if ($dataOrden[0]['esfacturado'] == 1) {
                        $imagen = "facturar.png";
                    }
                }
                if ($v["nombredoc"] == '4') {
                    $dataDevolucion[$i]['comprobante'] = "Guia Remision";
                    $serieComprobante = $devolucion->add_ceros($v["serie"], 3);
                    $correlativoComprobante = $v["numdoc"];
                }
                if ($dataOrden[0]['esfacturado'] == 0) {
                    if ($v["electronico"] == '1') {
                        $dataDevolucion[$i]['comprobante'] = "-";
                        $serieComprobante = '';
                        $correlativoComprobante = '';
                    }
                }
            }
            $dataDevolucion[$i]['simbolo'] = ($dataOrden[0]['IdMoneda'] == 1) ? "S/" : "US $";
            $dataDevolucion[$i]['icono'] = $dataOrden[0]['esfacturado'] == 1 ? '<img width="20" high="20" src="/imagenes/' . ($dataDevolucion[$i]['electronico'] == 1 ? 'facturarele.png' : 'facturar.png') . '">' : '-';
            $dataDevolucion[$i]['esfacturado'] = $serieComprobante . ' - ' . $correlativoComprobante;
            $dataDevolucion[$i]['codigov'] = $dataOrden[0]['codigov'];
            $serieComprobante = '';
            $correlativoComprobante = '';
        }
        $data['dataDevolucion'] = $dataDevolucion;
        $paginacion = $devolucion->paginadoDevoluciones("", $parametro);

        $data['retorno'] = $parametro;
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['totregistros'] = $devolucion->cuentaDevoluciones("", $parametro);

        $this->view->show('/devolucion/buscadevoluciones.phtml', $data);
    }

    function historialDevoluciones() {
        
    }

    function grabaFactura() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $iddevolucion = $_REQUEST['iddevolucion'];
        $iddocumento = $_REQUEST['idDocumento'];
        $data['electronico'] = 1;
        $data['iddocumento'] = $iddocumento;
        $filtro = "iddevolucion='$iddevolucion'";
        $exito = $devolucion->actualizarDevolucion($data, $filtro);
        echo $exito;
    }

    function grabaobservaciones() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $observaciones = $_REQUEST['observaciones'];
        $iddevolucion = $_REQUEST['iddevolucion'];
        $data['observaciones'] = $observaciones;
        $filtro = "iddevolucion='$iddevolucion'";
        $exito = $devolucion->actualizarDevolucion($data, $filtro);
        echo $exito;
    }

    function cargaobservaciones() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $iddevolucion = $_REQUEST['iddevolucion'];

        $filtro = "iddevolucion='$iddevolucion'";
        $data = $devolucion->listaDevolucionFiltro($filtro);

        echo $data[0]['observaciones'];
    }

    /*     * ************************************************************************
      Nombre : ReporteDevoluciones
      Funcionalidad: Muestra los criterios de busueda para las devoluciones,
      esta asociada a la funcion DataReporteDevoluciones
      Creado por: Fernando Garcia Atuncar
      Fecha :	19.10.2014
     * ************************************************************************* */
    function ReporteDevoluciones() {
        $this->view->newshow("/devolucion/reportedevolucion");
    }

    function DataReporteDevoluciones() {
        $idcliente = $_REQUEST['idcliente'];
        $idordenventa = $_REQUEST['idordenventa'];
        if ($_REQUEST['situacion'] != -1) {
            $esregistrado = $_REQUEST['situacion'] ? 1 : 1;
            $esaprobado = $_REQUEST['situacion'] ? 2 : 1;
        }
        $devtotal = $_REQUEST['devtotal'];
        $fecregini = $_REQUEST['fecregini'];
        $fecregfin = $_REQUEST['fecregfin'];
        $fecaprini = $_REQUEST['fecaprini'];
        $fecaprfin = $_REQUEST['fecaprfin'];
        $devoluciones = $this->AutoLoadModel('Devolucion');
        $dataDevoluciones = $devoluciones->ReporteDevoluciones($idcliente, $idordenventa, $esregistrado, $fecregini, $fecregfin, $esaprobado, $fecaprini, $fecaprfin, $devtotal);
        // echo "<pre>";
        // print_r($_REQUEST);
        // exit;

        $tamanio = count($dataDevoluciones);
        for ($i = 0; $i < $tamanio; $i++) {
            $simbolo = $dataDevoluciones[$i]['simbolo'];
            $acumula[$simbolo]['importetotal'] += $dataDevoluciones[$i]['importetotal'];
            $situacion = ($dataDevoluciones[$i]['importetotal'] == $dataDevoluciones[$i]['importeaprobado']) ? 'DEV TOTAL' : 'DEV PARCIAL';
            $html .= "<tr>
                        <td>" . $dataDevoluciones[$i]['razonsocial'] . "</td>
                        <td>" . $dataDevoluciones[$i]['codigov'] . "</td>
                        <td>" . $simbolo . " " . $dataDevoluciones[$i]['importeaprobado'] . "</td>
                        <td>" . $dataDevoluciones[$i]['devolucion'] . "</td>
                        <td>" . $dataDevoluciones[$i]['registrado'] . "</td>
                        <td>" . $dataDevoluciones[$i]['fecharegistrada'] . "</td>
                        <td>" . $dataDevoluciones[$i]['aprobado'] . "</td>
                        <td>" . $dataDevoluciones[$i]['fechaaprobada'] . "</td>
                        <td>" . $simbolo . " " . $dataDevoluciones[$i]['importetotal'] . "</td>
                        <td>" . $situacion . "</td>
                        <td>" . $dataDevoluciones[$i]['observaciones'] . "</td>
                     </tr>";
        }
        $html .= "<tr><td colspan='8'></td><td><b>Devolucion US $</b></td><td><b>US $ " . $acumula['US $']['importetotal'] . "</b></td><td></td>";
        $html .= "<tr><td colspan='8'></td><td><b>Devolucion S/.</b></td><td><b>S/. " . $acumula['S/']['importetotal'] . "</b></td><td></td>";
        echo $html;
    }

    function anulardevolucion() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $iddevolucion = $_REQUEST['id'];
        $condicion = "registrado=1 and estado=1 and aprobado=1 and iddevolucion='" . $iddevolucion . "'";
        $dataDevolucion = $devolucion->listaDevolucionFiltro($condicion);
        if (count($dataDevolucion) > 0) {
            $documento = $this->AutoLoadModel('documento');
            $filtro = "doc.nombredoc=5 and doc.electronico=1 and (doc.esCargado=1 or doc.esAnulado=1)";
            $documento->buscadocumentoxordenventa($dataDevolucion[0]['idordenventa'], $filtro);
            $exito = $devolucion->eliminarDevolucion($iddevolucion);
            if ($exito) {
                $exito2 = $devolucion->eliminarDetalleDevolucion($iddevolucion);
            }
        }
        //$devoluciones->anularDevolucion($iddevolucion, "");
        $_REQUEST['id'] = null;
        $this->listarDevolucionTotal();
    }

    function resumendevolucion() {
        $data['MotivoDevolucion'] = $this->configIniTodo('MotivoDevolucion');
        $actor = $this->AutoLoadModel('actorrol');
        $data['vendedor'] = $actor->actoresxRolxNombreSinconEstado(25);
        $this->view->show('/devolucion/resumendevolucion.phtml', $data);
    }

}

?>