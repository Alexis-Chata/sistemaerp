<?php

Class ventascontroller extends ApplicationGeneral {

    function creaguiaped() {
        $_SESSION['Autenticado'] = true;
        $vendedor = new Actor();
        $ordenVenta = new OrdenVenta();
        $url = "/" . $_REQUEST['url'];
        $data['Documento'] = $this->tipoDocumento();
        $data['VendedorRanking'] = $vendedor->listadoVendedoresTodosRanking();
        $data['Vendedor'] = $vendedor->listadoVendedoresTodos();
        $data['FormaPago'] = $this->formaPago();
        $data['CondicionLetra'] = $ordenVenta->condicionesletra();
        $data['TipoLetra'] = $this->tipoLetra();
        $data['ModoFacturacion'] = $this->modoFacturacion();
        //$data['Codigo']=$ordenVenta->generaCodigo();
        $data['codigov'] = $_REQUEST['id'];
        $this->view->show("/ventas/ordenventa.phtml", $data);
    }

    function listadoVendedoresTodos2() {
        $vendedor = new Actor();
        $listadoVendedoresTodos2 = $vendedor->listadoVendedoresTodos2();
        echo json_encode($listadoVendedoresTodos2);
    }

    function ventaxpress() {
        $_SESSION['Autenticado'] = true;
        $vendedor = new Actor();
        $ordenVenta = new OrdenVenta();
        $opciones = new general();
        $actorrol = new actorrol();
        $url = "/" . $_REQUEST['url'];
        $data['Documento'] = $this->tipoDocumento();
        $data['Vendedor'] = $vendedor->listadoVendedoresTodos();
        $data['FormaPago'] = $this->formaPago();
        $data['CondicionLetra'] = $ordenVenta->condicionesletra();
        $data['TipoLetra'] = $this->tipoLetra();
        $data['ModoFacturacion'] = $this->modoFacturacion();
        //$data['Codigo']=$ordenVenta->generaCodigo();
        $data['codigov'] = $_REQUEST['id'];
        $data['Despachador'] = $actorrol->actoresxRol(30);
        $data['Verificador'] = $actorrol->actoresxRol(31);
        $data['Opcion'] = $opciones->buscaOpcionexurl($url);
        $data['Modulo'] = $opciones->buscaModulosxurl($url);
        $this->view->show("/ventas/ventaxpress.phtml", $data);
    }

    function vendedores() {
        $this->view->show('/vendedor/proveedores.phtml');
    }

    function reportstocklin() {
        $_SESSION['Autenticado'] = true;
        $this->view->show("ventas/form.phtml");
    }

    //Reporte de stock valorizado
    function reportevalorizados() {
        $linea = new Linea();
        //$this->view->template="stockvalorizado";
        $data['Linea'] = $linea->listadoLineas();
        $this->view->show('reporte/stockvalorizado.phtml', $data);
    }

    function reporteStockValorizado() {
        $idLinea = $_REQUEST['linea'];
        $idSubLinea = $_REQUEST['sublinea'];
        $reporte = new Reporte();
        $data = $reporte->reporteStockValorizado($idLinea, $idSubLinea);
        $total = 0;
        for ($i = 0; $i < count($data); $i++) {
            echo '<tr>';
            echo    "<td>" . $data[$i]['codigo'] . "</td>";
            echo    "<td>" . $data[$i]['nompro'] . "</td>";
            echo    "<td>" . $data[$i]['idalmacen'] . "</td>";
            echo    "<td>" . $data[$i]['idlineapadre'] . "</td>";
            echo    "<td>" . $data[$i]['nomum'] . "</td>";
            echo    "<td>" . $data[$i]['stockactual'] . "</td>";
            echo    '<td class="right">' . number_format($data[$i]['preciolista'], 2) . '</td>';
            echo    '<td class="right">' . number_format(($data[$i]['stockactual'] * $data[$i]['preciolista']), 2) . '</td>';
            echo '<tr>';
            $total += ($data[$i]['stockactual'] * $data[$i]['preciolista']);
        }
        echo '<tr style="font-weight:bold"><td colspan="6"></td><td class="right">Total:</td><td class="right">' . number_format($total, 2) . '</td></tr>';
    }

    //mantenimiento cliente
    function mantclientes() {
        $_SESSION['Autenticado'] = true;
        $cliente = new Cliente();
        $data['Cliente'] = $cliente->listadoClientes();
        $this->view->show("cliente/listar.phtml", $data);
    }

    /* Autocomplete Transporte */
    function autocompleteTransporte() {
        $razonSocial = $_REQUEST['term'];
        $transporte = new Transporte();
        $data = $transporte->buscarAutocomplete($razonSocial);
        echo json_encode($data);
    }

    /*     * Aprobaciones */
    
    function autorizarventa() {
        if (!$_REQUEST['idOrdenVenta']) {
            $ordenVenta = new OrdenVenta();
            $opciones = new general();
            $url = "/" . $_REQUEST['url'];
            $data['Opcion'] = $opciones->buscaOpcionexurl($url);
            $data['Modulo'] = $opciones->buscaModulosxurl($url);
            $data['ordenVenta'] = $ordenVenta->pedidoxaprobar();
            $data['FormaPago'] = $this->formaPago();
            $this->view->show("ventas/aprobarpedido.phtml", $data);
        } else {
            $id = $_REQUEST['idOrdenVenta'];
            $ordenVenta = new OrdenVenta();
            if ($id != '' && $id != 0) {
                $dataBusqueda = $ordenVenta->buscarOrdenVentaxId($id);
            }
            if ($dataBusqueda[0]['vbventas'] != 1) {
                $estadoOrden = $_REQUEST['estadoOrden'];
                $dataOrdenVenta = $_REQUEST['Orden'];
                $dataDetalleOrdenVenta = $_REQUEST['DetalleOrdenVenta'];
                $detalleOrdenVenta = new DetalleOrdenVenta();
                $producto = new Producto();
                $dataOrdenVenta['vbventas'] = ($estadoOrden == 1) ? 1 : 2;
                if ($dataOrdenVenta['vbventas'] == 2) {
                    $dataOrdenVenta['desaprobado'] = 1;
                    $dataDuracion['referencia'] = 'ventas';
                } else {
                    $dataOrdenVenta['vbcobranzas'] = 1; // se actualiza vb de cobranzas
                    $dataDuracion['referencia'] = 'ventas/cobranzas';
                }
                $productos = $_REQUEST['Producto'];
                $exito1 = $ordenVenta->actualizaOrdenVenta($dataOrdenVenta, $id);
                $cont = 0;
                if ($exito1) {
                    foreach ($dataDetalleOrdenVenta as $data) {
                        if ($dataOrdenVenta['vbventas'] == 2 || $data['estado'] == 0) {
                            //buscamos producto
                            $idproducto = $productos[$cont]['idproducto'];
                            $dataProducto = $producto->buscaProductoxId($idproducto);
                            $stockdisponibleA = $dataProducto[0]['stockdisponible'];
                            $stockdisponibleN = $stockdisponibleA + $productos[$cont]['cantsolicitada'];
                            $dataNuevo['stockdisponible'] = $stockdisponibleN;
                            //actualizamos es stockdisponible
                            $exitoP = $producto->actualizaProducto($dataNuevo, $idproducto);
                        } elseif ($data['estado'] == 1 && $dataOrdenVenta['vbventas'] == 1) {
                            //buscamos producto
                            $idproducto = $productos[$cont]['idproducto'];
                            $dataProducto = $producto->buscaProductoxId($idproducto);
                            $stockdisponibleA = $dataProducto[0]['stockdisponible'];
                            $stockdisponibleN = $stockdisponibleA + $productos[$cont]['cantsolicitada'] - $data['cantaprobada'];
                            $dataNuevo['stockdisponible'] = $stockdisponibleN;
                            //actualizamos es stockdisponible
                            $exitoP = $producto->actualizaProducto($dataNuevo, $idproducto);
                        }
                        $data['precioofertado'] = $data['preciofinal'];
                        $exito2 = $detalleOrdenVenta->actualizar($data['iddetalleordenventa'], $data);
                        $cont++;
                    }
                    if ($exito2) {
                        $ordenVentaDuracion = new ordenventaduracion();
                        $DDA = $ordenVentaDuracion->listaOrdenVentaDuracion($id, "creacion");
                        $dataDuracion['idordenventa'] = $id;
                        $intervalo = $this->date_diff(date('Y-m-d H:i:s', strtotime($DDA[0]['fechacreacion'])), date('Y-m-d H:i:s'));
                        $dataDuracion['tiempo'] = $intervalo;
                        if (empty($DDA[0]['fechacreacion'])) {
                            $dataDuracion['tiempo'] = 'indefinido';
                        }
                        $exito3 = $ordenVentaDuracion->grabaOrdenVentaDuracion($dataDuracion);
                        $ruta['ruta'] = "/ventas/autorizarventa";
                        $this->view->show("ruteador.phtml", $ruta);
                        //$date3=date('Y-m-d H:i:s');
                        //$intervalo=$this->date_diff($date3,'2013-01-23 15:30:00');
                    }
                }
            } else {
                $ruta['ruta'] = "/ventas/autorizarventa";
                $this->view->show("ruteador.phtml", $ruta);
            }
        }
    }

    function listaordenes() {
        $ordenVenta = new OrdenVenta();
        $data['ordenVenta'] = $ordenVenta->listaOrdenesGeneral();
        $data['FormaPago'] = $this->formaPago();
        $this->view->show("/ventas/ordenesgeneral.phtml", $data);
    }

    function guiamadre() {
        $this->view->show("/ventas/guiamadre.phtml", $data);
    }

    function listaReporteVentasXdia() {
        $anio = $_REQUEST['opcanio'];
        $mes = $_REQUEST['opcmes'];
        $auxiliarFecha = strtotime($anio . "-" . $mes . "-01");
        $fin = date("t", $auxiliarFecha);
        $ordenVenta = new OrdenVenta();
        $reporte = $this->AutoLoadModel('reporte');
        $documento = $this->AutoLoadModel('documento');
        $ingresos = $this->AutoLoadModel('ingresos');
        $tipocambioprom = $this->AutoLoadModel('Tcpromedio');
        $equivalentes = $ingresos->equivalencias($mes, $anio);
        $valortc = round($tipocambioprom->getTipocambio($mes, $anio), 2);
        $totalFacturado = $equivalentes[0]['factura'];
        $totalnoFacturado = $equivalentes[0]['nofacturado'];
        $totalcob_letras = $equivalentes[0]['letra'];
        $totalcob_efecdepo = $equivalentes[0]['efectdesp'];
        $totalAprobado = 0;
        $totalDespachado = 0;
        $totalFacturadoTemp = 0;
        $totalnoFacturadoTemp = 0;
        $totalcob_letrasTemp = 0;
        $totalcob_efecdepoTemp = 0;
        for ($i = 1; $i <= $fin; $i++) {
            $fechabusqueda = $anio . "-" . $mes . "-" . str_pad($i, 2, "0", STR_PAD_LEFT);
            $arreglo[$i][0] = $documento->getMonto($fechabusqueda, '1', 2);
            $arreglo[$i][1] = $ordenVenta->montoGuiaRemision($fechabusqueda, 2);
            $arreglo[$i][2] = $ingresos->getMontoTotal($fechabusqueda, 2, "1, 2, 3, 4");
            $arreglo[$i][3] = $ingresos->getMontoTotal($fechabusqueda, 2, "5, 9");
            $arreglo[$i][0] += $documento->getMonto($fechabusqueda, '1', 1) / $valortc;
            $arreglo[$i][1] += $ordenVenta->montoGuiaRemision($fechabusqueda, 1) / $valortc;
            $arreglo[$i][2] += $ingresos->getMontoTotal($fechabusqueda, 1, "1, 2, 3, 4") / $valortc;
            $arreglo[$i][3] += $ingresos->getMontoTotal($fechabusqueda, 1, "5, 9") / $valortc;
            $totalFacturadoTemp += $arreglo[$i][0];
            $totalnoFacturadoTemp += $arreglo[$i][1];
            $totalcob_efecdepoTemp += $arreglo[$i][2];
            $totalcob_letrasTemp += $arreglo[$i][3];
        }
        if ($totalFacturado > $totalFacturadoTemp) {
            $totalFacturadoDif = $totalFacturado - $totalFacturadoTemp;
            $porc1 = $totalFacturadoTemp / $totalFacturado;
        } else {
            $totalFacturadoDif = $totalFacturadoTemp - $totalFacturado;
            $porc1 = $totalFacturado / $totalFacturadoTemp;
        }
        if ($totalnoFacturado > $totalnoFacturadoTemp) {
            $totalnoFacturadoDif = $totalnoFacturado - $totalnoFacturadoTemp;
            $porc2 = $totalnoFacturadoTemp / $totalnoFacturado;
        } else {
            $totalnoFacturadoDif = $totalnoFacturadoTemp - $totalnoFacturado;
            $porc2 = $totalnoFacturado / $totalnoFacturadoTemp;
        }
        if ($totalcob_efecdepo > $totalcob_efecdepoTemp) {
            $totalcob_efecdepoDif = $totalcob_efecdepo - $totalcob_efecdepoTemp;
            $porc3 = $totalcob_efecdepoTemp / $totalcob_efecdepo;
        } else {
            $totalcob_efecdepoDif = $totalcob_efecdepoTemp - $totalcob_efecdepo;
            $porc3 = $totalcob_efecdepo / $totalcob_efecdepoTemp;
        }
        if ($totalcob_letras > $totalcob_letrasTemp) {
            $totalcob_letrasDif = $totalcob_letras - $totalcob_letrasTemp;
            $porc4 = $totalcob_letrasTemp / $totalcob_letras;
        } else {
            $totalcob_letrasDif = $totalcob_letrasTemp - $totalcob_letras;
            $porc4 = $totalcob_letras / $totalcob_letrasTemp;
        }
        while ($totalFacturadoDif > 0 || $totalnoFacturadoDif > 0 || $totalcob_efecdepoDif > 0 || $totalcob_letrasDif > 0) {
            for ($i = 1; $i <= $fin; $i++) {
                if ($totalcob_efecdepo > $totalcob_efecdepoTemp) {
                    if ($totalcob_efecdepoDif > 0) {
                        if ($totalcob_efecdepoDif - $arreglo[$i][2] * $porc3 < 0) {
                            $arreglo[$i][2] = $arreglo[$i][2] + $totalcob_efecdepoDif;
                            $totalcob_efecdepoDif = 0;
                        } else {
                            $totalcob_efecdepoDif = $totalcob_efecdepoDif - $arreglo[$i][2] * $porc3;
                            $arreglo[$i][2] = $arreglo[$i][2] + $arreglo[$i][2] * $porc3;
                        }
                    }
                } else {
                    if ($totalcob_efecdepoDif > 0) {
                        if ($totalcob_efecdepoDif - $arreglo[$i][2] * $porc3 < 0) {
                            $arreglo[$i][2] = $arreglo[$i][2] - $totalcob_efecdepoDif;
                            $totalcob_efecdepoDif = 0;
                        } else {
                            $totalcob_efecdepoDif = $totalcob_efecdepoDif - $arreglo[$i][2] * $porc3;
                            $arreglo[$i][2] = $arreglo[$i][2] - $arreglo[$i][2] * $porc3;
                        }
                    }
                }
                if ($totalcob_letras > $totalcob_letrasTemp) {
                    if ($totalcob_letrasDif > 0) {
                        if ($totalcob_letrasDif - $arreglo[$i][3] * $porc4 < 0) {
                            $arreglo[$i][3] = $arreglo[$i][3] + $totalcob_letrasDif;
                            $totalcob_letrasDif = 0;
                        } else {
                            $totalcob_letrasDif = $totalcob_letrasDif - $arreglo[$i][3] * $porc4;
                            $arreglo[$i][3] = $arreglo[$i][3] + $arreglo[$i][3] * $porc4;
                        }
                    }
                } else {
                    if ($totalcob_letrasDif > 0) {
                        if ($totalcob_letrasDif - $arreglo[$i][3] * $porc4 < 0) {
                            $arreglo[$i][3] = $arreglo[$i][3] - $totalcob_letrasDif;
                            $totalcob_letrasDif = 0;
                        } else {
                            $totalcob_letrasDif = $totalcob_letrasDif - $arreglo[$i][3] * $porc4;
                            $arreglo[$i][3] = $arreglo[$i][3] - $arreglo[$i][3] * $porc4;
                        }
                    }
                }
                if ($totalFacturado > $totalFacturadoTemp) {
                    if ($totalFacturadoDif > 0) {
                        if ($totalFacturadoDif - $arreglo[$i][0] * $porc1 < 0) {
                            $arreglo[$i][0] = $arreglo[$i][0] + $totalFacturadoDif;
                            $totalFacturadoDif = 0;
                        } else {
                            $totalFacturadoDif = $totalFacturadoDif - $arreglo[$i][0] * $porc1;
                            $arreglo[$i][0] = $arreglo[$i][0] + $arreglo[$i][0] * $porc1;
                        }
                    }
                } else {
                    if ($totalFacturadoDif > 0) {
                        if ($totalFacturadoDif - $arreglo[$i][0] * $porc1 < 0) {
                            $arreglo[$i][0] = $arreglo[$i][0] - $totalFacturadoDif;
                            $totalFacturadoDif = 0;
                        } else {
                            $totalFacturadoDif = $totalFacturadoDif - $arreglo[$i][0] * $porc1;
                            $arreglo[$i][0] = $arreglo[$i][0] - $arreglo[$i][0] * $porc1;
                        }
                    }
                }
                if ($totalnoFacturado > $totalnoFacturadoTemp) {
                    if ($totalnoFacturadoDif > 0) {
                        if ($totalnoFacturadoDif - $arreglo[$i][1] * $porc2 < 0) {
                            $arreglo[$i][1] = $arreglo[$i][1] + $totalnoFacturadoDif;
                            $totalnoFacturadoDif = 0;
                        } else {
                            $totalnoFacturadoDif = $totalnoFacturadoDif - $arreglo[$i][1] * $porc2;
                            $arreglo[$i][1] = $arreglo[$i][1] + $arreglo[$i][1] * $porc2;
                        }
                    }
                } else {
                    if ($totalnoFacturadoDif > 0) {
                        if ($totalnoFacturadoDif - $arreglo[$i][1] * $porc2 < 0) {
                            $arreglo[$i][1] = $arreglo[$i][1] - $totalnoFacturadoDif;
                            $totalnoFacturadoDif = 0;
                        } else {
                            $totalnoFacturadoDif = $totalnoFacturadoDif - $arreglo[$i][1] * $porc2;
                            $arreglo[$i][1] = $arreglo[$i][1] - $arreglo[$i][1] * $porc2;
                        }
                    }
                }
            }
        }
        for ($i = 1; $i <= $fin; $i++) {
            $fechabusqueda = $anio . "-" . $mes . "-" . str_pad($i, 2, "0", STR_PAD_LEFT);
            $aprobado = $reporte->montoAprobado($fechabusqueda, 2);
            $despachado = $reporte->montoDespachado($fechabusqueda, 2);
            $aprobado += $reporte->montoAprobado($fechabusqueda, 1) / $valortc;
            $despachado += $reporte->montoDespachado($fechabusqueda, 1) / $valortc;
            $totalAprobado += $aprobado;
            $totalDespachado += $despachado;
            echo "<tr>";
            echo    "<td>" . ($i) . "</td>";
            echo    "<td>" . $fechabusqueda . "</td>";
            echo    "<td class='right'>" . number_format($aprobado, 2) . "</td>";
            echo    "<td class='right'>" . number_format($arreglo[$i][0], 2) . "</td>";
            echo    "<td class='right'>" . number_format($arreglo[$i][1], 2) . "</td>";
            echo    "<td class='right'>" . number_format($arreglo[$i][2], 2) . "</td>";
            echo    "<td class='right'>" . number_format($arreglo[$i][3], 2) . "</td>";
            echo    "<td class='right'>" . number_format($despachado, 2) . "</td>";
            echo "</tr>";
        }
        echo "<tr>";
        echo    "<th colspan='2'>Monto Total: </th>";
        echo    "<th>$. " . number_format($totalAprobado, 2) . "</th>";
        echo    "<th>$. " . number_format($totalFacturado, 2) . "</th>";
        echo    "<th>$. " . number_format($totalnoFacturado, 2) . "</th>";
        echo    "<th>$. " . number_format($totalcob_efecdepo, 2) . "</th>";
        echo    "<th>$. " . number_format($totalcob_letras, 2) . "</th>";
        echo    "<th>$. " . number_format($totalDespachado, 2) . "</th>";
        echo "</tr>";
    }

    function listaReporteVentas() {
        if (!empty($_REQUEST['txtFechaAprobadoInicio'])) {
            $txtFechaAprobadoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaAprobadoInicio']));
        }
        if (!empty($_REQUEST['txtFechaAprobadoFinal'])) {
            $txtFechaAprobadoFinal = date('Y-m-d', strtotime($_REQUEST['txtFechaAprobadoFinal']));
        }
        if (!empty($_REQUEST['txtFechaGuiadoInicio'])) {
            $txtFechaGuiadoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaGuiadoInicio']));
        }
        if (!empty($_REQUEST['txtFechaGuiadoFin'])) {
            $txtFechaGuiadoFin = date('Y-m-d', strtotime($_REQUEST['txtFechaGuiadoFin']));
        }
        if (!empty($_REQUEST['txtFechaDespachoInicio'])) {
            $txtFechaDespachoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaDespachoInicio']));
        }
        if (!empty($_REQUEST['txtFechaDespachoFin'])) {
            $txtFechaDespachoFin = date('Y-m-d', strtotime($_REQUEST['txtFechaDespachoFin']));
        }
        if (!empty($_REQUEST['txtFechaCanceladoInicio'])) {
            $txtFechaCanceladoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaCanceladoInicio']));
        }
        if (!empty($_REQUEST['txtFechaCanceladoFin'])) {
            $txtFechaCanceladoFin = date('Y-m-d', strtotime($_REQUEST['txtFechaCanceladoFin']));
        }
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $idCliente = $_REQUEST['idCliente'];
        $idVendedor = $_REQUEST['idVendedor'];
        $idpadre = $_REQUEST['idpadre'];
        $idcategoria = $_REQUEST['idcategoria'];
        $idzona = $_REQUEST['idzona'];
        $condicion = $_REQUEST['condicion'];
        $aprobados = $_REQUEST['aprobados'];
        $desaprobados = $_REQUEST['desaprobados'];
        $pendiente = $_REQUEST['pendiente'];
        $idmoneda = $_REQUEST['idmoneda'];
        $condVenta = $_REQUEST['condVenta'];
        $filtrocliente = $_REQUEST['filtrocliente'];
        $condicionVenta = "";
        if ($condicion == 1) {
            $condicionVenta = " and ov.es_contado='1' and ov.es_credito!='1' and ov.es_letras!='1' ";
        } elseif ($condicion == 2) {
            $condicionVenta = " and ov.es_credito='1' and ov.es_letras!='1' ";
        } elseif ($condicion == 3) {
            $condicionVenta = "  and ov.es_letras='1' and  ov.tipo_letra=1";
        } elseif ($condicion == 4) {
            $condicionVenta = "  and ov.es_letras='1' and ov.tipo_letra=2";
        }
        $reporte = $this->AutoLoadModel('reporte');
        $dataIdVendedor = $reporte->reporteVendedores($idVendedor, $txtFechaGuiadoInicio, $txtFechaGuiadoFin);
        $cantidadVendedor = count($dataIdVendedor);
        $fila = "";
        echo "<thead>";
        echo    "<tr>";
        echo        "<th style='width: 12%;'>Nombre Vendedor</th>";
        echo        "<th>Fecha Guiado</th>";
        echo        "<th>Fecha Despacho</th>";
        echo        "<th>Fecha Cancelado</th>";
        echo        "<th>Orden venta</th>";
        echo        "<th>Nombre Cliente</th>";
        echo        "<th>Estado</th>";
        echo        "<th>Condicion Venta</th>";
        echo        "<th>Detalle</th>";
        echo        "<th>Situacion</th>";
        echo        "<th>Importe Aprobado S/.</th>";
        echo        "<th>Importe Despachado S/.</th>";
        echo        "<th>Importe Devuelto S/.</th>";
        echo    "</tr>";
        echo "</thead>";
        echo "<tbody>";
        $generalAprobado = 0; //DOLARES
        $generalDespachado = 0; //DOLARES
        $generalDevolucion = 0; //DOLARES
        $generalAprobadoSol = 0;
        $generalDespachadoSol = 0;
        $generalDevolucionSol = 0;
        for ($x = 0; $x < $cantidadVendedor; $x++) {
            $totalAprobado = 0; //DOLARES
            $totalDespachado = 0; //DOLARES
            $totalDevolucion = 0;
            $totalAprobadoSol = 0;
            $totalDespachadoSol = 0;
            $totalDevolucionSol = 0;
            $dataReporte = $reporte->reporteVentas($txtFechaAprobadoInicio, $txtFechaAprobadoFinal, $txtFechaGuiadoInicio, $txtFechaGuiadoFin, $txtFechaDespachoInicio, $txtFechaDespachoFin, $txtFechaCanceladoInicio, $txtFechaCanceladoFin, $idOrdenVenta, $idCliente, $dataIdVendedor[$x]['idactor'], $idpadre, $idcategoria, $idzona, $condicionVenta, $aprobados, $desaprobados, $pendiente, $idmoneda, $condVenta, $filtrocliente);
            $cantidad = count($dataReporte);
            echo "<tr>";
            echo    "<td style='width: 12%;'><h4>" . $dataIdVendedor[$x]['nombres'] . " " . $dataIdVendedor[$x]['apellidopaterno'] . " " . $dataIdVendedor[$x]['apellidomaterno'] . "</h4></td>";
            echo "</tr>";
            for ($i = 0; $i < $cantidad; $i++) {
                $situtacion = "";
                if ($dataReporte[$i]['es_contado'] == 1 && $dataReporte[$i]['es_credito'] != 1 && $dataReporte[$i]['es_letras'] != 1) {
                    $situtacion = "Contado";
                } elseif ($dataReporte[$i]['es_credito'] == 1 && $dataReporte[$i]['es_letras'] != 1) {
                    $situtacion = "Credito";
                } elseif ($dataReporte[$i]['es_letras'] == 1 && $dataReporte[$i]['tipo_letra'] == 1) {
                    $situtacion = "Letra Banco";
                } elseif ($dataReporte[$i]['es_letras'] == 1 && $dataReporte[$i]['tipo_letra'] == 2) {
                    $situtacion = "Letra Cartera";
                }
                $estado = "Pendiente";
                if ($dataReporte[$i]['desaprobado'] == 1) {
                    $estado = "Desaprobado";
                } elseif ($dataReporte[$i]['vbcreditos'] == 1) {
                    $estado = "Aprobado";
                }
                if ($dataReporte[$i]['vbcreditos'] != 1) {
                    $valorImporte = 0.00;
                } else {
                    $valorImporte = $dataReporte[$i]['importeov'];
                }
                echo "<tr>";
                echo    "<td></td>";
                echo    "<td>" . $dataReporte[$i]['fordenventa'] . "</td>";
                echo    "<td>" . $dataReporte[$i]['fechadespacho'] . "</td>";
                echo    "<td>" . $dataReporte[$i]['fechaCancelado'] . "</td>";
                echo    "<td>" . $dataReporte[$i]['codigov'] . "</td>";
                echo    "<td>" . $dataReporte[$i]['razonsocial'] . "</td>";
                echo    "<td>" . ($estado) . "</td>";
                echo    "<td>" . $situtacion . "</td>";
                echo    "<td>" . (html_entity_decode($dataReporte[$i]['observaciones'], ENT_QUOTES, 'UTF-8')) . "</td>";
                echo    "<td>" . $dataReporte[$i]['estadoov'] . "</td>";
                echo    "<td style='text-align:right;'>" . $dataReporte[$i]['simbolo'] . " " . $dataReporte[$i]['importeaprobado'] . "</td>";
                echo    "<td style='text-align:right;'>" . $dataReporte[$i]['simbolo'] . " " . $valorImporte . "</td>";
                echo    "<td style='text-align:right;'>" . $dataReporte[$i]['simbolo'] . " " . $dataReporte[$i]['importedevolucion'] . "</td>";
                echo "</tr>";
                if ($dataReporte[$i]['simbolo'] == 'US $') {
                    $totalAprobado += $dataReporte[$i]['importeaprobado'];
                    $totalDespachado += $valorImporte;
                    $totalDevolucion += $dataReporte[$i]['importedevolucion'];
                } else {
                    $totalAprobadoSol += $dataReporte[$i]['importeaprobado'];
                    $totalDespachadoSol += $valorImporte;
                    $totalDevolucionSol += $dataReporte[$i]['importedevolucion'];
                }
            }
            echo "<tr><td colspan='9'></td>"
                    . "<th  style='text-align:right;'>TOTALES US $.</th>"
                    . "<td style='text-align:right;'>US $." . $totalAprobado . "</td>"
                    . "<td style='text-align:right;'>US $." . $totalDespachado . "</td>"
                    . "<td style='text-align:right;'>US $." . $totalDevolucion . "</td>"
                . "</tr>";
            echo "<tr><td colspan='9'></td>"
                    . "<th  style='text-align:right;'>TOTALES S/.</th>"
                    . "<td style='text-align:right;'>S/." . $totalAprobadoSol . "</td>"
                    . "<td style='text-align:right;'>S/." . $totalDespachadoSol . "</td>"
                    . "<td style='text-align:right;'>S/." . $totalDevolucionSol . "</td>"
                . "</tr>";
            $generalAprobado += $totalAprobado;
            $generalDespachado += $totalDespachado;
            $generalDevolucion += $totalDevolucion;
            $generalAprobadoSol += $totalAprobadoSol;
            $generalDespachadoSol += $totalDespachadoSol;
            $generalDevolucionSol += $totalDevolucionSol;
        }
        echo "<tfoot>";
        echo    "<tr><td colspan='8'></td><th colspan='2' style='text-align:center;'>TOTALES GENERALES US $.:</th><td style='text-align:right;'>US $." . $generalAprobado . "</td><td style='text-align:right;'>US $." . $generalDespachado . "</td><td style='text-align:right;'>US $." . $generalDevolucion . "</td></tr>";
        echo    "<tr><td colspan='8'></td><th colspan='2' style='text-align:center;'>TOTALES GENERALES S/.:</th><td style='text-align:right;'>S/." . $generalAprobadoSol . "</td><td style='text-align:right;'>S/." . $generalDespachadoSol . "</td><td style='text-align:right;'>US $." . $generalDevolucionSol . "</td></tr>";
        echo    "<tr><td></td></tr>";
        echo "</tfoot>";
        echo "</tbody>";
    }

    function utilidadxContenedor() {
        $this->view->show("/ventas/utilidadxContenedor.phtml");
    }

    function intereses() {
        $arrayIntereses = $this->configIniTodo('Intereses');
        if ($_REQUEST['iddetalleordencobro'] > 0 && $_REQUEST['intereses'] > 0) {
            $iddetalleordencobro = $_REQUEST['iddetalleordencobro'];
            $interes = $_REQUEST['intereses'];
            $detallecobro = $this->AutoLoadModel('detalleordencobro');
            $dataDoc = $detallecobro->buscaDetalleOrdencobro($iddetalleordencobro);
            echo 'entro';
            if (count($dataDoc) > 0) {
                echo 'entro1';
                if (empty($dataDoc[0]['situacion']) && $dataDoc[0]['formacobro'] == '3' && !empty($dataDoc[0]['numeroletra'])) {
                    echo 'entro2';
                    $dias = $detallecobro->cantidad_dias_entre_dos_fechas($dataDoc[0]['fechagiro'], $dataDoc[0]['fvencimiento']);
                    if ($dias > 75) {
                        echo 'entro3';
                        if (isset($arrayIntereses[$interes])) {
                            echo 'entro4';
                            $porcentajeInteres = $arrayIntereses[$interes] / 100;
                            $valorIntereses = round($dataDoc[0]['importedoc'] * $porcentajeInteres, 2);
                            $dataDocAct['importedoc'] = $dataDoc[0]['importedoc'] + $valorIntereses;
                            $dataDocAct['saldodoc'] = $dataDoc[0]['saldodoc'] + $valorIntereses;
                            $dataDocAct['interes'] = $dataDoc[0]['interes'] + $valorIntereses;
                            $dataDocAct['situacion'] = '';
                            $dataDocAct['tipogasto'] = 4;
                            $detallecobro->actualizar_cargado($dataDocAct, $iddetalleordencobro);
                            $ordencobromodel = $this->AutoLoadModel('ordencobro');
                            $dataOrdenCobro = $ordencobromodel->buscaOrdencobro($dataDoc[0]['idordencobro']);
                            if (count($dataOrdenCobro) > 0) {
                                echo 'entro5';
                                $dataOCAct['importeordencobro'] = $dataOrdenCobro[0]['importeordencobro'] + $valorIntereses;
                                $dataOCAct['saldoordencobro'] = $dataOrdenCobro[0]['saldoordencobro'] + $valorIntereses;
                                $dataOCAct['situacion'] = 'Pendiente';
                                $ordencobromodel->actualizaOrdencobro($dataOCAct, $dataDoc[0]['idordencobro']);
                                $ordenGasto = $this->AutoLoadModel('ordengasto');
                                $dataOrdenGasto = $ordenGasto->buscaxFiltro("idordenventa='" . $dataOrdenCobro[0]['idordenventa'] . "' and idtipogasto=4 and estado=1");
                                if (count($dataOrdenGasto) > 0) {
                                    $dataOG['importegasto'] = round($valorIntereses + $dataOrdenGasto[0]['importegasto'], 2);
                                    $exitoOG = $ordenGasto->actualiza($dataOG, $dataOrdenGasto[0]['idordengasto']);
                                } else {
                                    $dataOG['importegasto'] = round($valorIntereses, 2);
                                    $dataOG['idordenventa'] = $dataOrdenCobro[0]['idordenventa'];
                                    $dataOG['idtipogasto'] = 4;
                                    $exitoOG = $ordenGasto->graba($dataOG);
                                }
                                $documento = new Documento();
                                $dataDocumento = $documento->buscaDocumento('', "idordenventa='" . $dataOrdenCobro[0]['idordenventa'] . "' and nombredoc=7 and numdoc='" . $dataDoc[0]['numeroletra'] . "'");
                                if (count($dataDocumento) > 0) {
                                    $DataDocuAct['montofacturado'] = $dataDoc[0]['importedoc'] + $valorIntereses;
                                    $documento->actualizarDocumento($DataDocuAct, "iddocumento='" . $dataDocumento[0]['iddocumento'] . "'");
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $data['Intereses'] = $arrayIntereses;
            $this->view->show("/ventas/intereses.phtml", $data);
        }
    }

    function ventasverificarprecio2() {
        $idProducto = $_REQUEST['idproducto'];
        $producto = new Producto();
        $dataProducto = $producto->buscaProducto($idProducto);
        $tempreferencia = '';
        if (count($dataProducto) > 0) {
            $tempreferencia .= '<tr>' .
                                    '<td style="text-align: center;"><b>Precio Lista Soles:</b></td>' .
                                    '<td style="text-align: center;">S/ ' . $dataProducto[0]['preciolista'] . '</td>' .
                                    '<td style="text-align: center;"><b>Precio Lista Dolares:<b></td>' .
                                    '<td style="text-align: center;">US $ ' . $dataProducto[0]['preciolistadolares'] . '</td>' .
                               '</tr>' . 
                               '<tr>' .
                                    '<td style="text-align: center;"><b>Precio Lista Soles (30%):</b></td>' .
                                    '<td style="text-align: center;">S/ ' . round(($dataProducto[0]['preciolista'] - $dataProducto[0]['preciolista']*0.3), 2) . '</td>' .
                                    '<td style="text-align: center;"><b>Precio Lista Dolares (30%):<b></td>' .
                                    '<td style="text-align: center;">US $ ' . round(($dataProducto[0]['preciolistadolares'] - $dataProducto[0]['preciolistadolares']*0.3), 2) . '</td>' .
                               '</tr>';
            $dataRespuesta['codigo'] = $dataProducto[0]['codigopa'];
            $dataRespuesta['nompro'] = str_replace('"', '\"', $dataProducto[0]['nompro']);
        } else {
            $dataRespuesta['codigo'] = 'No disponible';
            $dataRespuesta['nompro'] = 'No disponible';
        }
        $dataRespuesta['referencia'] = $tempreferencia;
        echo json_encode($dataRespuesta);
    }
    
    function ventasverificarprecio() {
        $moneda = $_REQUEST['moneda'];
        $precio = $_REQUEST['precio'];
        $idproducto = $_REQUEST['idproducto'];
        $producto = new Producto();
        $dataProducto = $producto->buscaProducto($idproducto);
        $rspta = 0;
        if (count($dataProducto) > 0) { 
            $rsptamoneda = 1;
            $rspta = 2;
            if ($moneda == 1) {
                if ($precio < (($dataProducto[0]['preciolista']-0.01) - $dataProducto[0]['preciolista']*0.3)) {
                    $rspta = 1;
                }
            } else if ($moneda == 2) {
                if ($precio < (($dataProducto[0]['preciolistadolares']-0.01) - $dataProducto[0]['preciolistadolares']*0.3)) {
                    $rspta = 1;
                }
            } else {
                $rsptamoneda = 0;
            }
            $dataRespuesta['rsptamoneda'] = $rsptamoneda;
        }
        $dataRespuesta['rspta'] = $rspta;
        echo json_encode($dataRespuesta);
    }

}

?>