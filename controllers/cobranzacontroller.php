<?php

Class cobranzacontroller extends ApplicationGeneral {

    private $mostrar = 10;

    function Listado() {
        $_SESSION['Autenticado'] = true;
        $this->view->show("cobranza/listaordenpago.phtml");
    }

    function estcuentas() {
        $cliente = new Cliente();
        $opciones = new general();
        $ordenventa = new OrdenVenta();
        $vendedor = new Actor();
        $producto = new Producto();
        $clientetransporte = new clienteTransporte();
        $this->view->show("/cobranza/listacuentas.phtml");
    }

    function retornarVentas() {
        $idordenventa = $_REQUEST['idordenventa'];
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $data['mventas'] = 'RETORNADO POR COBRANZAS';
        $data['vbventas'] = '-1';
        $exito = $ordenventa->actualizaOrdenVenta($data, $idordenventa);
        if ($exito) {
            $detalleordenventa = $this->AutoLoadModel('detalleordenventa');
            $producto = $this->AutoLoadModel('producto');
            $dataOrdenVenta = $detalleordenventa->listaDetalleOrdenVentaYOrden($idordenventa);
            $cantidad = count($dataOrdenVenta);
            for ($i = 0; $i < $cantidad; $i++) {
                $cantidadsolicitada = $dataOrdenVenta[$i]['cantsolicitada'];
                $cantidadaprobada = $dataOrdenVenta[$i]['cantaprobada'];
                $idproducto = $dataOrdenVenta[$i]['idproducto'];
                $iddetalleordenventa = $dataOrdenVenta[$i]['iddetalleordenventa'];
                $dataProducto = $producto->buscaProducto($idproducto);
                $stockdisponible = $dataProducto[0]['stockdisponible'];
                $nuevoStockDisponible = $stockdisponible + $cantidadaprobada - $cantidadsolicitada;
                $datos['stockdisponible'] = $nuevoStockDisponible;
                $actualizar = $producto->actualizaProducto($datos, $idproducto);
            }
            if ($actualizar) {
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

    function cuentas() {
        $ordenventa = new OrdenVenta();
        $idcliente = $_REQUEST['id'];
        $dordenventa = $ordenventa->listadoOrdenVentaxidcliente2($idcliente);
        $total = count($dordenventa);
        $cuenta = array();
        $indice = 0;
        for ($i = 0; $i < $total; $i++) {
            $saldo = $ordenventa->saldoxidordenventa($dordenventa[$i]['idordenventa']);
            $fvencimiento = $ordenventa->ultimafechaxidordenventa($dordenventa[$i]['idordenventa']);
            $cuenta[$indice]['codigov'] = $dordenventa[$i]['codigov'];
            $cuenta[$indice]['importeov'] = number_format($dordenventa[$i]['importeov'], 4);
            $cuenta[$indice]['idcondicionletra'] = $dordenventa[$i]['idcondicionletra'];
            $cuenta[$indice]['situacion'] = (($saldo > 0) ? 'PENDIENTE' : 'CANCELADO');
            $cuenta[$indice]['fvencimiento'] = $fvencimiento;
            $cuenta[$indice]['saldo'] = number_format($saldo, 4);
            $cuenta[$indice]['importedoc'] = "";
            $cuenta[$indice]['formacobro'] = "";
            $cuenta[$indice]['situacionc'] = "";
            $cuenta[$indice]['fvencimientoc'] = "";
            $indice += 1;
            $dcuenta = $ordenventa->cuentasxidordenventa($dordenventa[$i]['idordenventa']);
            $total2 = count($dcuenta);
            for ($j = 0; $j < $total2; $j++) {
                $cuenta[$indice]['codigov'] = $dordenventa[$i]['codigov'];
                $cuenta[$indice]['importeov'] = "";
                $cuenta[$indice]['idcondicionletra'] = "";
                $cuenta[$indice]['situacion'] = "";
                $cuenta[$indice]['fvencimiento'] = "";
                $cuenta[$indice]['saldo'] = "";
                $cuenta[$indice]['importedoc'] = number_format($dcuenta[$j]['importedoc'], 4);
                $cuenta[$indice]['formacobro'] = (($dcuenta[$j]['formacobro'] == 1) ? 'CONTADO' : (($dcuenta[$j]['formacobro'] == 3) ? 'LETRA' : 'CREDITO'));
                $cuenta[$indice]['situacionc'] = (($dcuenta[$j]['situacion'] == 0) ? 'PENDIENTE' : (($dcuenta[$j]['situacion'] == 1) ? 'CANCELADO' : 'DESDOBLADO'));
                $cuenta[$indice]['fvencimientoc'] = $dcuenta[$j]['fvencimiento'];
                $indice += 1;
            }
        }
        $objeto = $this->formatearparakui($cuenta);
        header("Content-type: application/json");
        //echo "{\"data\":" .json_encode($objeto). "}";
        echo json_encode($objeto);
    }

    function nuevo() {
        $id = $_REQUEST['id'];
        $ordenventa = new ordenVenta();
        $cli = new Cliente();
        $ac = new Actor();
        $orden = new Orden();
        $zona = new Zona();
        $data['Cliente'] = $cli->listadoClientes();
        $data['Vendedor'] = $ac->listadoVendedoresTodos();
        $data['OrdenPago'] = $ordenventa->listado();
        $data['Zona'] = $zona->listado();
        $this->view->show("/cobranza/nuevo.phtml", $data);
    }

    function graba() {
        $data = $_REQUEST['Cuenta'];
        $c = new Cuenta();
        $exito = $c->graba($data);
        if ($exito) {
            $ruta['ruta'] = "/cobranza/estcuentas";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function editar() {
        $id = $_REQUEST['id'];
        $c = new Cuenta();
        $ac = new Actor();
        $guia = new OrdenVenta();
        $zona = new Zona();
        $data['Zona'] = $zona->listado();
        $data['Cuenta'] = $c->buscar($id);
        $data['Cliente'] = $ac->listadocliente();
        $data['Vendedor'] = $ac->listadoVendedores();
        $data['Guia'] = $guia->listado();
        //$this->view->template="movimiento";
        $this->view->show("/cobranza/editar.phtml", $data);
    }

    function actualiza() {
        $id = $_REQUEST['idCuenta'];
        $data = $_REQUEST['Cuenta'];
        $c = new Cuenta();
        $exito = $c->actualiza($data, "idcuenta=" . $id);
        if ($exito) {
            //$this->view->template="movimiento";
            $ruta['ruta'] = "/cobranza/estcuentas";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function eliminar() {
        $id = $_REQUEST['id'];
        $c = new Cuenta();
        $exito = $c->eliminar($id);
        if ($exito) {
            //$this->view->template="movimiento";
            $ruta['ruta'] = "/cobranza/estcuentas";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function reportvendedor() {
        $_SESSION['Autenticado'] = true;
        $c = new Cuenta();
        $totalRegistro = $c->contarCuenta();
        $pagina = ($_REQUEST['id']) ? $_REQUEST['id'] : 1;
        $inicio = ($pagina - 1) * $this->mostrar;
        $paginas = ceil($totalRegistro / $this->mostrar);
        $paginacion = array('Paginas' => $paginas, 'Pagina' => $pagina);
        $data['Paginacion'] = $paginacion;

        $actor = new Actor();
        $vend = $actor->listadovendedores();
        $data['Vendedores'] = $vend;
        //$this->view->template="movimiento";
        $this->view->show("cobranza/listacuentasxvendedor.phtml", $data);
    }

    function reportzonageografica() {
        $_SESSION['Autenticado'] = true;
        $c = new Cuenta();
        $z = new Zona();
        $totalRegistro = $c->contarCuenta();
        $pagina = ($_REQUEST['id']) ? $_REQUEST['id'] : 1;
        $inicio = ($pagina - 1) * $this->mostrar;
        $paginas = ceil($totalRegistro / $this->mostrar);
        $paginacion = array('Paginas' => $paginas, 'Pagina' => $pagina);
        $data['Paginacion'] = $paginacion;
        $data['Zonas'] = $z->listado();
        //$this->view->template="movimiento";
        $this->view->show("cobranza/listacuentasxzona.phtml", $data);
    }

    function cuentascobrar() {
        $ordenventa = new OrdenVenta();
        $totalRegistro = $cuenta->contarCuenta();
        $pagina = ($_REQUEST['id']) ? $_REQUEST['id'] : 1;
        $inicio = ($pagina - 1) * $this->mostrar;
        $paginas = ceil($totalRegistro / $this->mostrar);
        $paginacion = array('Paginas' => $paginas, 'Pagina' => $pagina);
        $data['cuenta'] = $cuenta->listadoCuentasxCobrar($inicio, $this->mostrar);
        $data['Paginacion'] = $paginacion;
        //$this->view->template="movimiento";
        $this->view->show("/cobranza/listacuentasxcobrar.phtml", $data);
    }

    function cargarListadoxVendedor() {
        $id = $_REQUEST['id'];
        $c = new Cuenta();
        $z = new Zona();
        $actor = new Actor();
        $data = $c->listarxvendedor($id);
        $tot = count($data);
        for ($i = 0; $i < $tot; $i++) {
            $cliente = $actor->buscarxid($data[$i]['idcliente']);
            $zona = $z->buscarxid($data[$i]['idzona']);
            echo "<tr>";
            echo    "<td>" . $data[$i]['importe'] . "</td>";
            echo    "<td>" . $data[$i]['fvencimiento'] . "</td>";
            echo    "<td>" . $data[$i]['tipo'] . "</td>";
            echo    "<td><a href=\"/cobranza/editar/" . $data[$i]['idcuenta'] .
                    "\"><img src=\"/imagenes/editar.gif\"></a></td>";
            echo    "<td><a href=\"/cobranza/eliminar/" . $data[$i]['idcuenta'] .
                    "\"><img src=\"/imagenes/eliminar.gif\"></a></td>";
            echo "</tr>";
        }
    }

    function cargarListadoxZona() {
        $id = $_REQUEST['id'];
        $c = new Cuenta();
        $data = $c->listarxzona($id);
        $z = new Zona();
        $actor = new Actor();
        $tot = count($data);
        for ($i = 0; $i < $tot; $i++) {
            $cliente = $actor->buscarxid($data[$i]['idcliente']);
            $vendedor = $actor->buscarxid($data[$i]['idvendedor']);
            $zona = $z->buscarxid($data[$i]['idzona']);
            echo "<tr>";
            echo    "<td>" . $data[$i]['importe'] . "</td>";
            echo    "<td>" . $data[$i]['fvencimiento'] . "</td>";
            echo    "<td>" . $data[$i]['tipo'] . "</td>";
            echo    "<td><a href=\"/cobranza/editar/" . $data[$i]['idcuenta'] . "\">
				<img src=\"/imagenes/editar.gif\"></a></td>";
            echo    "<td><a href=\"/cobranza/eliminar/" . $data[$i]['idcuenta'] . "\">
				<img src=\"/imagenes/eliminar.gif\"></a></td>";
            echo "</tr>";
        }
    }

    function cargarOrdenesPago() {
        $id = $_REQUEST['id'];
        $pago = new OrdenPago();
        if (isset($id) && $id != "") {
            $data = $pago->listarxguia($id);
            $tot = count($data);
            for ($i = 0; $i < $tot; $i++) {
                echo "<tr id=" . $data[$i]['idordenpago'] . ">";
                echo    "<td>" . $data[$i]['idordenpago'] . "</td>";
                echo    "<td>" . $data[$i]['importe'] . "</td>";
                echo    "<td>" . $data[$i]['fvencimiento'] . "</td>";
                echo    "<td>" . $data[$i]['tipo'] . "</td>";
                echo    "<td><a href=\"/cobranza/editar/" . $data[$i]['idcuenta'] .
                        "\"><img src=\"/imagenes/editar.gif\" title=\"click para editar\"></a></td>";
                echo    "<td><a href=\"/cobranza/eliminar/" . $data[$i]['idcuenta'] .
                        "\"><img src=\"/imagenes/eliminar.gif\" title=\"click para eliminar\"></a></td>";
                echo "</tr>";
            }
        }
    }

    function cargarLetrasxGuia() {
        $id = $_REQUEST['id'];
        $letra = new Letras();
        if (isset($id) && $id != "") {
            $data = $letra->listarxguia($id);
            $actor = new Actor();
            $tot = count($data);
            for ($i = 0; $i < $tot; $i++) {
                echo "<tr>";
                echo    "<td>" . $data[$i]['codigoletra'] . "</td>";
                echo    "<td>" . $data[$i]['fechaemision'] . "</td>";
                echo    "<td>" . $data[$i]['condicionletra'] . "</td>";
                echo    "<td>" . $data[$i]['tipoletra'] . "</td>";
                echo    "<td><a href=\"/cobranza/editarletra/" . $data[$i]['idletra'] .
                        "\"><img src=\"/imagenes/editar.gif\"></a></td>";
                echo    "<td><a href=\"/cobranza/eliminarletra/" . $data[$i]['idletra'] .
                        "\"><img src=\"/imagenes/eliminar.gif\"></a></td>";
                echo "</tr>";
            }
        }
    }

    /*  */

    function cargarCuentasxOrdenVenta() {
        $id = $_REQUEST['id'];
        $ordenventa = new OrdenVenta();
        if (isset($id) && $id != "") {
            $data = $ordenventa->cuentasxidordenventa($id);
            $tot = count($data);
            for ($i = 0; $i < $tot; $i++) {
                echo "<tr>";
                echo    "<td>" . $data[$i]['codigoov'] . "</td>";
                echo    "<td>" . $data[$i]['importedoc'] . "</td>";
                echo    "<td>" . $data[$i]['condicionletra'] . "</td>";
                echo    "<td>" . $data[$i]['formacobro'] . "</td>";
                echo "</tr>";
            }
        }
    }

    function cargarGuiasxCliente() {
        $idcliente = $_REQUEST['id'];
        $pagina = $_REQUEST['pagina'];
        $paginacion = $_REQUEST['paginacion'];
        if (isset($idcliente) && $idcliente != "") {
            $ordenventa = new OrdenVenta();
            $vendedor = new Actor();
            $clientetransporte = new clienteTransporte();
            $z = new Zona();
            $dordenventa = $ordenventa->listadoOrdenVentaxidcliente($idcliente);
            $total = count($dordenventa);
            for ($i = 0; $i < $total; $i++) {
                echo "<tr>";
                echo    "<td>" . $dordenventa[$i]['codigov'] . "</td>";
                echo    "<td>" . $dordenventa[$i]['importeov'] . "</td>";
                echo    "<td>" . $dordenventa[$i]['saldoactual'] . "</td>";
                echo    "<td>" . $dordenventa[$i]['situacion'] . "</td>";
                echo "</tr>";
                $dcuenta = $ordenventa->cuentasxidordenventa($dordenventa[$i]['idordenventa']);
                $total2 = count($dcuenta);
                for ($j = 0; $j < $total2; $j++) {
                    echo "<tr>";
                    echo    "<td></td>";
                    echo    "<td></td>";
                    echo    "<td></td>";
                    echo    "<td></td>";
                    echo    "<td>" . $dcuenta[$j]['importedoc'] . "</td>";
                    echo    "<td>" . $dcuenta[$j]['condicionletra'] . "</td>";
                    echo    "<td>" . $dcuenta[$j]['formacobro'] . "</td>";
                    echo "</tr>";
                }
            }
        }
    }

    function cargardatosguia() {
        $id = $_REQUEST['id'];
        $guiaped = new OrdenVenta();
        $vendedor = new Vendedor;
        $z = new Zona();
        if (isset($id) && $id != "") {
            $data = $guiaped->buscaOrdenVenta($id);
            $tot = count($data);
            $cliente = $actor->buscarxid($data[0]['idcliente']);
            $vendedor = $actor->buscarxid($data[0]['idvendedor']);
            $zona = $z->buscarxid($data[0]['idzona']);
            echo "<fieldset><b><legend>Detalles de la Orden de Pedido:</legend></b>";
            echo "<li>Fecha de Emision: " . $data[0]['femision'] . "<li>Fecha de Compra: " .
            $data[0]['fcompra'] . "<li>Fecha Recibida: " . $data[0]['frecibida'];
            echo "<li>Cliente: " . $cliente[0]['nombres'] . " " . $cliente[0]['apellidopaterno']
            . " " . $cliente[0]['apellidomaterno'];
            echo "<li>Zona: " . $zona[0]['nombre'];
            echo "<li>Vendedor: " . $vendedor[0]['nombres'] . " " . $vendedor[0]['apellidopaterno'] . " " . $vendedor[0]['apellidomaterno'];
            echo "</fieldset>";
        }
    }

    /* Aprobaciones */

    function aprobarPedido() {
        if (!isset($_REQUEST['idOrden'])) {
            $orden = new OrdenVenta();
            $data['ordenVenta'] = $orden->pedidoxaprobar(2);
            $data['FormaPago'] = $this->formaPago();
            $this->view->show("cobranza/aprobarpedido.phtml", $data);
        } else {
            $id = $_REQUEST['idOrden'];
            $estadoOrden = $_REQUEST['estadoOrden'];
            $dataOrdenVenta = $_REQUEST['Orden'];
            $ordenVenta = new OrdenVenta();
            $producto = new Producto();
            $dataOrdenVenta['vbcobranzas'] = $estadoOrden;
            if ($estadoOrden == 2) {
                $dataOrdenVenta['desaprobado'] = 1;
            }
            $productos = $_REQUEST['Producto'];
            $cantidadProducto = count($productos);
            $exito1 = $ordenVenta->actualizaOrdenVenta($dataOrdenVenta, $id);
            if ($exito1) {
                if ($estadoOrden == 2) {
                    for ($i = 0; $i < $cantidadProducto; $i++) {
                        //buscamos producto
                        $idproducto = $productos[$i]['idproducto'];
                        $dataProducto = $producto->buscaProductoxId($idproducto);
                        $stockdisponibleA = $dataProducto[0]['stockdisponible'];
                        $stockdisponibleN = $stockdisponibleA + $productos[$i]['cantaprobada'];
                        $dataNuevo['stockdisponible'] = $stockdisponibleN;
                        //actualizamos es stockdisponible
                        $exitoP = $producto->actualizaProducto($dataNuevo, $idproducto);
                    }
                }
                $ordenVentaDuracion = new ordenventaduracion();
                $DDA = $ordenVentaDuracion->listaOrdenVentaDuracion($id, "ventas");
                $dataDuracion['idordenventa'] = $id;
                $intervalo = $this->date_diff(date('Y-m-d H:i:s', strtotime($DDA[0]['fechacreacion'])), date('Y-m-d H:i:s'));
                $dataDuracion['tiempo'] = $intervalo;
                $dataDuracion['referencia'] = 'cobranza';
                if (empty($DDA[0]['fechacreacion'])) {
                    $dataDuracion['tiempo'] = 'indefinido';
                }
                $exito3 = $ordenVentaDuracion->grabaOrdenVentaDuracion($dataDuracion);
                $ruta['ruta'] = "/cobranza/aprobarpedido";
                $this->view->show("ruteador.phtml", $ruta);
            }
        }
    }

    function estadoguia() {
        $this->view->show("cobranza/estadoguia.phtml", $data);
    }

    function cobranzaVendedor() {
        $reporte = $this->AutoLoadModel('reporte');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $idPadre = $_REQUEST['lstCategoriaPrincipal'];
        $idCategoria = $_REQUEST['lstZonaCobranza'];
        $idZona = $_REQUEST['lstZona'];
        $idVendedor = $_REQUEST['idVendedor'];
        $idCliente = $_REQUEST['idCliente'];
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $situacion = $_REQUEST['lstSituacion'];
        $fechaInicio = $_REQUEST['txtFechaInicio'];
        $fechaFin = $_REQUEST['txtFechaFin'];
        $tipoCobranza = $_REQUEST['lstTipoCobranza'];
        if ($situacion == "cancelado" or $situacion == "anulado") {
            $situ = " and wc_ordenventa.`situacion`='$situacion' and wc_detalleordencobro.`situacion`='cancelado' ";
        } elseif ($situacion == "pendiente") {
            $situ = " and wc_ordenventa.`situacion`='$situacion' and wc_detalleordencobro.`situacion`='' ";
        } else {
            $situ = " and wc_detalleordencobro.`situacion`='' ";
        }
        if (!empty($idCliente)) {
            $filtro = " wc_cliente.`idcliente`='$idCliente' ";
        }
        $data = $reporte->reportclienteCobro($filtro, $idZona, $idPadre, $idCategoria, $idVendedor, $tipoCobranza, $fechaInicio, $fechaFinal, $situ);
        // print_r($data);
        // exit;
        $cantidad = count($data);
        $idOrden = 0;
        $fila = "";
        $total = 0;
        $totImporte = 0;
        $totPagado = 0;
        $totDevuelto = 0;
        $fila .= "<tr>";
        $fila .=    "<th>Orden Venta</th>";
        $fila .=    "<th>F.Despacho</th>";
        $fila .=    "<th>F.Vencimiento</th>";
        $fila .=    "<th>Cod. Ven.</th>";
        $fila .=    "<th colspan='4'>Cliente</th>";
        $fila .=    "<th>Imp. Guia</th>";
        $fila .=    "<th>Imp. Devuelto</th>";
        $fila .=    "<th>Imp. Pagado</th>";
        $fila .=    "<th>Imp. Deuda</th>";
        $fila .= "</tr>";
        if ($cantidad > 0) {
            for ($i = 0; $i < $cantidad; $i++) {
                if ($idOrden != $data[$i]['idordenventa']) {
                    $idOrden = $data[$i]['idordenventa'];
                    $importe = $ordenGasto->totalGuia($data[$i]['idordenventa']);
                    $total += $importe;
                    $fila .= "<tr style='background:skyblue;border:solid 1px;'>";
                    $fila .=    "<td>" . $data[$i]['codigov'] . "</td>";
                    $fila .=    "<td>" . date('Y/m/d', strtotime($data[$i]['fechadespacho'])) . "</td>";
                    $fila .=    "<td>" . date('Y/m/d', strtotime($data[$i]['fechavencimiento'])) . "</td>";
                    $fila .=    "<td>" . $data[$i]['codigoa'] . "</td>";
                    $fila .=    "<td colspan='4'>" . $data[$i]['razonsocial'] . "</td>";
                    $fila .=    "<td>" . $data[$i]['simbolomoneda'] . " " . number_format($importe, 2) . "</td>";
                    $fila .=    "<td>" . $data[$i]['simbolomoneda'] . " " . number_format($data[$i]['importedevolucion'], 2) . "</td>";
                    $fila .=    "<td>" . $data[$i]['simbolomoneda'] . " " . number_format($data[$i]['importepagado'], 2) . "</td>";
                    $fila .=    "<td>" . $data[$i]['simbolomoneda'] . " " . number_format($importe - $data[$i]['importepagado'], 2) . "</td>";
                    $fila .= "</tr>";
                    $acumulaxMoneda[$data[$i]['simbolomoneda']]['totImporte'] += round($importe, 2);
                    //$totImporte+=round($importe,2);
                    $acumulaxMoneda[$data[$i]['simbolomoneda']]['totPagado'] += round($data[$i]['importepagado'], 2);
                    //$totPagado+=round($data[$i]['importepagado'],2);
                    $acumulaxMoneda[$data[$i]['simbolomoneda']]['totDevuelto'] += round($data[$i]['importedevolucion'], 2);
                    //$totDevuelto+=round($data[$i]['importedevolucion'],2);
                }
                if ($data[$i]['formacobro'] == 1) {
                    $data[$i]['formacobro'] = "Contado";
                } elseif ($data[$i]['formacobro'] == 2) {
                    $data[$i]['formacobro'] = "Credito";
                } elseif ($data[$i]['formacobro'] == 3) {
                    $data[$i]['formacobro'] = "Letra";
                }
                if ($idOrden == $data[$i]['idordenventa'] and ( $situacion == "pendiente" or $situacion == "")) {
                    $fila .= "<tr>";
                    $fila .=    "<td>" . $data[$i][''] . "</td>";
                    $fila .=    "<td>" . $data[$i]['formacobro'] . "</td>";
                    $fila .=    "<td>" . $data[$i]['numeroletra'] . "</td>";
                    $fila .=    "<td>" . $data[$i]['fvencimiento'] . "</td>";
                    $fila .=    "<td colspan='2'>" . number_format($data[$i]['importedoc']) . "</td>";
                    $fila .=    "<td>" . number_format($data[$i]['saldodoc'], 2) . "</td>";
                    $fila .=    "<td>" . $data[$i]['recepcionletras'] . "</td>";
                    $fila .=    "<td>" . $data[$i][''] . "</td>";
                    $fila .= "</tr>";
                }
            }
            $fila .= "<tr>";
            $fila .=    "<td colspan='9'>&nbsp; </td>";
            $fila .= "</tr>";
            $fila .= "<tr>";
            $fila .=    "<td colspan='9'>&nbsp; ACUMULADO EN SOLES ( S/.) </td>";
            $fila .= "</tr>";
            $fila .= "<tr>";
            $fila .=    "<th>Total Guia</th>";
            $fila .=    "<td>" . number_format($acumulaxMoneda['S/']['totImporte'], 2) . "</td>";
            $fila .=    "<th>Total Devuelto</th>";
            $fila .=    "<td>" . number_format($acumulaxMoneda['S/']['totDevuelto'], 2) . "</td>";
            $fila .=    "<th colspan='2'>Total Pagado</th>";
            $fila .=    "<td>" . number_format($acumulaxMoneda['S/']['totPagado'], 2) . "</td>";
            $fila .=    "<th>Total Deuda</th>";
            $fila .=    "<td>" . number_format($acumulaxMoneda['S/']['totImporte'] - $acumulaxMoneda['S/']['totPagado'], 2) . "</td>";
            $fila .= "</tr>";

            $fila .= "<tr>";
            $fila .=    "<td colspan='9'>&nbsp; ACUMULADO EN DOLARES ( US $) </td>";
            $fila .= "</tr>";
            $fila .= "<tr>";
            $fila .=    "<th>Total Guia</th>";
            $fila .=    "<td>" . number_format($acumulaxMoneda['US $']['totImporte'], 2) . "</td>";
            $fila .=    "<th>Total Devuelto</th>";
            $fila .=    "<td>" . number_format($acumulaxMoneda['US $']['totDevuelto'], 2) . "</td>";
            $fila .=    "<th colspan='2'>Total Pagado</th>";
            $fila .=    "<td>" . number_format($acumulaxMoneda['US $']['totPagado'], 2) . "</td>";
            $fila .=    "<th>Total Deuda</th>";
            $fila .=    "<td>" . number_format($acumulaxMoneda['US $']['totImporte'] - $acumulaxMoneda['US $']['totPagado'], 2) . "</td>";
            $fila .= "</tr>";
        } else {
            
        }
        echo $fila;
    }

    function letrasprotestadasxdia() {
        $zona = $this->AutoLoadModel('zona');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['hijo'] = $zona->listacategoriaHijo();
        $data['zona'] = $zona->listadoTotalZona();
        $this->view->show('/cobranza/protestadaspordia.phtml', $data);
    }

    function letrassinenviaralbanco() {
        $zona = $this->AutoLoadModel('zona');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['hijo'] = $zona->listacategoriaHijo();
        $data['zona'] = $zona->listadoTotalZona();
        $this->view->show('/cobranza/letrassinenviaralbanco.phtml', $data);
    }

    function letrassinenviaralbancoevaluacion() {
        $zona = $this->AutoLoadModel('zona');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['hijo'] = $zona->listacategoriaHijo();
        $data['zona'] = $zona->listadoTotalZona();
        $this->view->show('/cobranza/letrassinenviaralbancoenevaluacion.phtml', $data);
    }
    
    function pendientesporvendedor() {
        $zona = $this->AutoLoadModel('zona');
        $vendedor = new Actor();
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['hijo'] = $zona->listacategoriaHijo();
        $data['zona'] = $zona->listadoTotalZona();
        $data['Vendedor'] = $vendedor->listadoVendedoresTodos();
        $this->view->show('/cobranza/pendientesporvendedor.phtml',$data);
    }

    function eliminarprotesto() {
        if (!empty($_REQUEST['id'])) {
            $iddetalleordecobro = $_REQUEST['id'];
            $detalleordencobro = new detalleOrdenCobro();
            $ordencobro = new OrdenCobro();
            $ordengasto = new Ordengasto();
            $dataDOC = $detalleordencobro->buscaDetalleOrdencobroespecial($iddetalleordecobro);
            if (count($dataDOC) > 0) {
                $protesto = $dataDOC[0]['montoprotesto'];
                $nuevosaldodoc = $dataDOC[0]['saldodoc'] - $protesto;
                if ($protesto > 0 && $nuevosaldodoc >= 0) {
                    if ($nuevosaldodoc == 0) {
                        $dataDOCNew['situacion'] = 'cancelado';
                    } else {
                        $dataDOCNew['situacion'] = $dataDOC[0]['situacion'];
                    }
                    $dataDOCNew['montoprotesto'] = 0;
                    $dataDOCNew['saldodoc'] = $nuevosaldodoc;
                    $dataDOCNew['importedoc'] = $dataDOC[0]['importedoc'] - $protesto;
                    $detalleordencobro->actualizaDetalleOrdencobro($dataDOCNew, $iddetalleordecobro);
                    $dataOCNew['importeordencobro'] = $dataDOC[0]['importeordencobro'] - $protesto;
                    $dataOCNew['saldoordencobro'] = $dataDOC[0]['saldoordencobro'] - $protesto;
                    if ($dataOCNew['saldoordencobro'] == 0) {
                        $dataOCNew['situacion'] = 'cancelado';
                    } else {
                        $dataOCNew['situacion'] = $dataDOC[0]['situacionoc'];
                    }
                    $ordencobro->actualizaOrdencobro($dataOCNew, $dataDOC[0]['idordencobro']);
                    $dataOGnew['importegasto'] = $dataDOC[0]['importegasto'] - $protesto;
                    $ordengasto->actualiza($dataOGnew, $dataDOC[0]['idordengasto']);
                } else {
                    echo 'Revisa el detalle orden cobro';
                }
            }
        }
    }

}

?>