<?php

class MovimientoController extends ApplicationGeneral {

    private $mostrar = 5;

    function probando() {
        $var_config = parse_ini_file("config.ini", true);
        $tipo = 'Salida';
        $conceptos = $var_config[$tipo];
        print_r($conceptos);
    }

    function resumendekardex() {
        $this->view->show('/movimiento/resumendekardex.phtml', $data);
    }

    function nuevoKardex() {
        $this->view->show('/movimiento/nuevoKardex.phtml', $data);
    }

    function JIngresoMovimiento() {
        $this->view->show('/movimiento/jingresoMovimiento.phtml', $data);
    }

    function jingresarDevolucion() {
        $this->view->show('/movimiento/jingresarDevolucion.phtml', $data);
    }

    function jingresarBoleta() {
        $this->view->show('/movimiento/jingresarBoleta.phtml', $data);
    }

    function nuevo() {
        $rutaImagen = $this->rutaImagenesProducto();
        $documentoTipo = $this->AutoLoadModel('documentotipo');
        $movimiento = new Movimiento();
        $linea = new Linea();
        //$data['numeroMovimiento']=$movimiento->generaCodigo();
        $data['Tipomovimiento'] = $this->tipoMovimiento();
        $data['RutaImagen'] = $rutaImagen;
        $data['documentoTipo'] = $documentoTipo->listadoDocumentoTipo();
        $this->view->show("/movimiento/nuevo.phtml", $data);
    }

    function repuesto() {
        $rutaImagen = $this->rutaImagenesProducto();
        $documentoTipo = $this->AutoLoadModel('documentotipo');
        $movimiento = new Movimiento();
        $linea = new Linea();
        //$data['numeroMovimiento']=$movimiento->generaCodigo();
        $data['Tipomovimiento'] = $this->tipoMovimiento();
        $data['RutaImagen'] = $rutaImagen;
        $data['documentoTipo'] = $documentoTipo->listadoDocumentoTipo();
        $this->view->show("/movimiento/repuesto.phtml", $data);
    }

    function listaConceptoMovimiento() {
        $idTipoMovimiento = $_REQUEST['id'];
        $tipomovimiento = $this->AutoLoadModel('tipooperacion');
        $conceptoMomiviento = $tipomovimiento->listadoTipoOperacion($idTipoMovimiento);
        for ($i = 0; $i < count($conceptoMomiviento); $i++) {
            if ($idTipoMovimiento == 1) {
                if ($conceptoMomiviento[$i]['idtipooperacion'] != 2 && $conceptoMomiviento[$i]['idtipooperacion'] != 5) {
                    echo '<option value="' . ($conceptoMomiviento[$i]['idtipooperacion']) . '">' . $conceptoMomiviento[$i]['nombre'];
                }
            } else {
                if ($conceptoMomiviento[$i]['idtipooperacion'] != 1) {
                    echo '<option value="' . ($conceptoMomiviento[$i]['idtipooperacion']) . '">' . $conceptoMomiviento[$i]['nombre'];
                }
            }
        }
    }

    function movstock() {
        $rutaImagen = $this->rutaImagenesProducto();
        $movimiento = new Movimiento();
        $tipoMovimiento = new Tipomovimiento();
        $linea = new Linea();
        $data['numeroMovimiento'] = $movimiento->contarMovimiento();
        $data['Tipomovimiento'] = $tipoMovimiento->listadoTiposmovimiento();
        $data['Linea'] = $linea->listadoLineas();
        $data['RutaImagen'] = $rutaImagen;
        $this->view->template = "movimiento";
        $this->view->show("/movimiento/nuevo.phtml", $data);
    }

    function registra() {
        $dataMovimiento = $_REQUEST['Movimiento'];
        $dataDetalleMovimiento = $_REQUEST['Detallemovimiento'];
        $producto = new Producto();
        $movimiento = new Movimiento();
        $detalleMovimiento = new Detallemovimiento();
        $dataMovimiento['idtipooperacion'] = $dataMovimiento['conceptomovimiento'];
        if (!empty($dataMovimiento['iddocumentotipo'])) {
            $dataMovimiento['essunat'] = 1;
        }
        $exitoMovimiento = $movimiento->grabaMovimiento($dataMovimiento);
        if ($exitoMovimiento) {
            $operacion = $dataMovimiento['tipomovimiento'];
            foreach ($dataDetalleMovimiento as $data) {
                $idProducto = $data['idproducto'];
                $dataBusqueda = $producto->buscaProducto($idProducto);
                if ($operacion == 2) {
                    $valor = $dataBusqueda[0]['stockactual'] - $data['cantidad'];
                    if ($valor <= 0) {
                        $stockNuevo['esagotado'] = 1;
                        $stockNuevo['fechaagotado'] = date('Y-m-d');
                    }
                    $stockNuevo['esagotado'] = 0;
                    $stockNuevo['fechaagotado'] = null;
                    $stockNuevo['stockactual'] = $valor;
                    $stockNuevo['stockdisponible'] = $dataBusqueda[0]['stockdisponible'] - $data['cantidad'];
                } elseif ($operacion == 1) {
                    $stockNuevo['esagotado'] = 0;
                    $stockNuevo['fechaagotado'] = null;
                    $stockNuevo['stockactual'] = $dataBusqueda[0]['stockactual'] + $data['cantidad'];
                    $stockNuevo['stockdisponible'] = $dataBusqueda[0]['stockdisponible'] + $data['cantidad'];
                }
                //$stockNuevo=($operacion=='+')?array('esagotado'=>0,'stockactual'=>($data['stockactual']+$data['cantidad']),'stockdisponible'=>($data['stockdisponibledm']+$data['cantidad'])):array('stockactual'=>($data['stockactual']-$data['cantidad']),'stockdisponible'=>($data['stockdisponibledm']-$data['cantidad']));
                $exitoProducto = $producto->actualizaProducto($stockNuevo, $data['idproducto']);
                $data2['stockactual'] = $stockNuevo['stockactual'];
                $data2['idmovimiento'] = $exitoMovimiento;
                $data2['preciovalorizado'] = $dataBusqueda[0]['preciocosto'];
                $data2['importe'] = $data['cantidad'] * $dataBusqueda[0]['preciocosto'];
                $data2['stockdisponibledm'] = $stockNuevo['stockdisponible'];
                $data2['idproducto'] = $data['idproducto'];
                $data2['cantidad'] = $data['cantidad'];
                $data2['pu'] = $dataBusqueda[0]['preciocosto'];
                $exitoDetalleMovimiento = $detalleMovimiento->grabaDetalleMovimieto($data2);
            }
        }
        if ($exitoDetalleMovimiento and $exitoProducto) {
            $ruta['ruta'] = "/almacen/movstock";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function registraRepuesto() {
        $dataMovimiento = $_REQUEST['Repuesto'];
        $dataDetalleMovimiento = $_REQUEST['Detallemovimiento'];
        $producto = new Producto();
        $movimiento = new Movimiento();
        $detalleMovimiento = new Detallemovimiento();
        //$dataMovimiento['idtipooperacion'] = $dataMovimiento['conceptomovimiento'];
        if (!empty($dataMovimiento['iddocumentotipo'])) {
            $dataMovimiento['essunat'] = 1;
        }
        $exitoMovimiento = $movimiento->grabaMovimientoRep($dataMovimiento);
        if ($exitoMovimiento) {
            $operacion = $dataMovimiento['tipomovimiento'];
            foreach ($dataDetalleMovimiento as $data) {
                $idProducto = $data['idproducto'];
                $dataBusqueda = $producto->buscaProducto($idProducto);
                if ($operacion == 2) {
                    $valor = $dataBusqueda[0]['stockactual'] - $data['cantidad'];
                    if ($valor <= 0) {
                        $stockNuevo['esagotado'] = 1;
                        $stockNuevo['fechaagotado'] = date('Y-m-d');
                    }
                    $stockNuevo['esagotado'] = 0;
                    $stockNuevo['fechaagotado'] = null;
                    $stockNuevo['stockactual'] = $valor;
                    $stockNuevo['stockdisponible'] = $dataBusqueda[0]['stockdisponible'] - $data['cantidad'];
                } elseif ($operacion == 1) {
                    $stockNuevo['esagotado'] = 0;
                    $stockNuevo['fechaagotado'] = null;
                    $stockNuevo['stockactual'] = $dataBusqueda[0]['stockactual'] + $data['cantidad'];
                    $stockNuevo['stockdisponible'] = $dataBusqueda[0]['stockdisponible'] + $data['cantidad'];
                }
                //$stockNuevo=($operacion=='+')?array('esagotado'=>0,'stockactual'=>($data['stockactual']+$data['cantidad']),'stockdisponible'=>($data['stockdisponibledm']+$data['cantidad'])):array('stockactual'=>($data['stockactual']-$data['cantidad']),'stockdisponible'=>($data['stockdisponibledm']-$data['cantidad']));
                $exitoProducto = $producto->actualizaProducto($stockNuevo, $data['idproducto']);
                $data2['stockactual'] = $stockNuevo['stockactual'];
                $data2['idrepuesto'] = $exitoMovimiento;
                $data2['preciovalorizado'] = $dataBusqueda[0]['preciocosto'];
                $data2['importe'] = $data['cantidad'] * $dataBusqueda[0]['preciocosto'];
                $data2['stockdisponibledm'] = $stockNuevo['stockdisponible'];
                $data2['idproducto'] = $data['idproducto'];
                $data2['observacion'] = $data['observacion'];
                $data2['cantidad'] = $data['cantidad'];
                $data2['pu'] = $dataBusqueda[0]['preciocosto'];
                $exitoDetalleMovimiento = $detalleMovimiento->grabaDetalleMovimietoRep($data2);
            }
        }
        if ($exitoDetalleMovimiento and $exitoProducto) {
            $ruta['ruta'] = "/almacen/movstockrep";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function detalle() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = $_REQUEST['idcontenedor'];
        }
        $detalleMovimiento = new Detallemovimiento();
        $data = $detalleMovimiento->buscaDetalleMovimiento($id);
        for ($i = 0; $i < count($data); $i++) {
            echo "<tr>";
            echo    "<td>" . $data[$i]['codigopa'] . "</td>";
            echo    "<td>" . $data[$i]['nompro'] . "</td>";
            echo    "<td>" . $data[$i]['observaciones'] . "</td>";
            echo    "<td>" . $data[$i]['cantidad'] . "</td>";
            echo "</tr>";
        }
    }

    function detalleRepuesto() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = $_REQUEST['idcontenedor'];
        }
        $detalleMovimiento = new Detallemovimiento();
        $data = $detalleMovimiento->buscaDetalleMovimientoRep($id);
        for ($i = 0; $i < count($data); $i++) {
            echo "<tr>";
            echo    "<td>" . $data[$i]['codigopa'] . "</td>";
            echo    "<td>" . $data[$i]['nompro'] . "</td>";
            echo    "<td>" . $data[$i]['observaciones'] . "</td>";
            echo    "<td>" . $data[$i]['cantidad'] . "</td>";
            echo "</tr>";
        }var_dump($data);
    }

    function KardexBuscaProducto() {
        $data['mes'] = $this->meses();
        $this->view->show("/movimiento/kardex.phtml", $data);
    }

    function kardexValorizadoxProducto() {
        $idproducto = $_REQUEST['id'];
        $mesInicial = !empty($_REQUEST['mesInicial']) ? $_REQUEST['mesInicial'] : 1;
        $mesFinal = !empty($_REQUEST['mesFinal']) ? $_REQUEST['mesFinal'] : 12;
        $anoInicial = !empty($_REQUEST['anoInicial']) ? $_REQUEST['anoInicial'] : date('Y');
        $anoFinal = !empty($_REQUEST['anoFinal']) ? $_REQUEST['anoFinal'] : date('Y');
        $sunat = $_REQUEST['sunat'];
        $_REQUEST['fecha1oo'] = $fecha1;
        $movimiento = new Movimiento();
        if ($_REQUEST['id']) {
            $dataKardex = $movimiento->kardexValorizadoxProducto($idproducto, $anoInicial, $anoFinal, $mesInicial, $mesFinal, $sunat);
            $total = count($dataKardex);
        }
        $tecant = 0;
        $tecosto = 0;
        $tscant = 0;
        $tscosto = 0;
        $cont = 0;
        if ($dataKardex[0]['codigotipooperacion'] != 16) {
            if ($dataKardex[0]['tipomovimiento'] == 1) {
                $cantidad = round($dataKardex[0]['SaldoCantidad'] - round($dataKardex[0]['cantidad']));
                if ($cantidad < 0) {
                    $cantidad = 0;
                }
            } else {
                $cantidad = round($dataKardex[0]['SaldoCantidad'] + round($dataKardex[0]['cantidad']));
            }
            echo "<tr>";
            echo    "<td></td>";
            echo    "<td colspan='4'>Saldo Inicial</td>";
            echo    "<td>16</td>";
            echo    "<td>" . $cantidad . "</td>";
            echo    "<td>" . $dataKardex[0]['SaldoPrecio'] . "</td>";
            echo    "<td>" . round($dataKardex[0]['SaldoPrecio'] * $cantidad, 2) . "</td>";
            echo    "<td></td>";
            echo    "<td></td>";
            echo    "<td></td>";
            echo    "<td>" . $cantidad . "</td>";
            echo    "<td>" . $dataKardex[0]['SaldoPrecio'] . "</td>";
            echo    "<td>" . round($dataKardex[0]['SaldoPrecio'] * $cantidad, 2) . "</td>";
            echo "</tr>";
        }
        for ($i = 0; $i < $total; $i++) {
            echo "<tr>";
            if ($dataKardex[$i]['codigotipooperacion'] == 16) {
                echo    "<td></td>";
                echo    "<td colspan='4'>Saldo Inicial</td>";
            } else {
                $cont++;
                echo    "<td>" . ($cont) . "</td>";
                echo    "<td>" . $dataKardex[$i]['fechamovimiento'] . "</td>";
                echo    "<td>" . $dataKardex[$i]['codigotipodocumento'] . "</td>";
                echo    "<td>" . $dataKardex[$i]['serie'] . "</td>";
                echo    "<td>" . $dataKardex[$i]['ndocumento'] . "</td>";
            }

            echo    "<td style='text-align:center'>" . $dataKardex[$i]['codigotipooperacion'] . "</td>";
            echo    "<td style='text-align:center'>" . $dataKardex[$i]['EntradaCantidad'] . "</td>";
            echo    "<td style='text-align:right'>" . (empty($dataKardex[$i]['EntradaPrecio']) ? '' : number_format($dataKardex[$i]['EntradaPrecio'], 2)) . "</td>";
            echo    "<td style='text-align:right'>" . (empty($dataKardex[$i]['EntradaCosto']) ? '' : number_format($dataKardex[$i]['EntradaCosto'], 2)) . "</td>";
            echo    "<td style='text-align:center'>" . $dataKardex[$i]['SalidaCantidad'] . "</td>";
            echo    "<td style='text-align:right'>" . (empty($dataKardex[$i]['SalidaPrecio']) ? '' : number_format($dataKardex[$i]['SalidaPrecio'], 2)) . "</td>";
            echo    "<td style='text-align:right'>" . (empty($dataKardex[$i]['SalidaCosto']) ? '' : number_format($dataKardex[$i]['SalidaCosto'], 2)) . "</td>";
            echo    "<td style='text-align:center'>" . round($dataKardex[$i]['SaldoCantidad']) . "</td>";
            echo    "<td style='text-align:right'>" . (empty($dataKardex[$i]['SaldoPrecio']) ? '' : number_format($dataKardex[$i]['SaldoPrecio'], 2)) . "</td>";
            echo    "<td style='text-align:right'>" . (empty($dataKardex[$i]['SaldoCosto']) ? '' : number_format($dataKardex[$i]['SaldoCosto'], 2)) . "</td>";
            echo "</tr>";
            $tecant += $dataKardex[$i]['EntradaCantidad'];
            $tecosto += $dataKardex[$i]['EntradaCosto'];
            $tscant += $dataKardex[$i]['SalidaCantidad'];
            $tscosto += $dataKardex[$i]['SalidaCosto'];
        }
        echo "<tr>";
        echo    "<td colspan=6></td>";
        echo    "<th style='text-align:center'>" . round($tecant) . "</td>";
        echo    "<td></td>";
        echo    "<th style='text-align:right'>" . number_format($tecosto, 2) . "</td>";
        echo    "<th style='text-align:center'>" . round($tscant) . "</td>";
        echo    "<td></td>";
        echo    "<th style='text-align:right'>" . number_format($tscosto, 2) . "</td>";
        echo    "<td colspan=3></td>";
        echo "</tr>";
    }

    /*     * ********************************************************************************** */
    function kardexProductoPedidoContabilidad1() {
        $idproducto = $_REQUEST['id'];
        $mesInicial = !empty($_REQUEST['mesInicial']) ? $_REQUEST['mesInicial'] : 1;
        $mesFinal = !empty($_REQUEST['mesFinal']) ? $_REQUEST['mesFinal'] : 12;
        $anoInicial = !empty($_REQUEST['anoInicial']) ? $_REQUEST['anoInicial'] : date('Y');
        $anoFinal = !empty($_REQUEST['anoFinal']) ? $_REQUEST['anoFinal'] : date('Y');
        $sunat = $_REQUEST['sunat'];
        $_REQUEST['fecha1oo'] = $fecha1;
        $movimiento = new Movimiento();
        if ($_REQUEST['id']) {
            $dataKardex = $movimiento->kardexNuevaConsultaContabilidad($idproducto, $anoInicial, $anoFinal, $mesInicial, $mesFinal, $sunat);
            $total = count($dataKardex);
            $movInicial = $movimiento->movimientoInicial($idproducto);
            $totalMov = count($movInicial);
        }
        $cont = 0;
        for ($i = 0; $i < $totalMov; $i++) {
            $cantidadTotal = $movInicial[$i]['stock'];
            $costoUnitario = $movInicial[$i]['pc'];
        }
//        echo "aa".$cantidadTotal."<br>".$costoUnitario."<br>";
        //OBTENER SALDO INICIAL INICIAL
//        $saldoInicialInicio = 100;
//        $cantidadTotal = 1500;
//        $costoUnitario = 3.5;
        $costoTotal = $cantidadTotal * $costoUnitario;
//        echo "cant:".$totalMov."---";
//        var_dump($movInicial);
//        if ($dataKardex[0]['codigotipooperacion'] != 16) {
//            if ($dataKardex[0]['tipomovimiento'] == 1) {
//                $cantidad = round($dataKardex[0]['SaldoCantidad'] - round($dataKardex[0]['cantidad']));
//                if ($cantidad < 0) {
//                    $cantidad = 0;
//                }
//            } else {
//                $cantidad = round($dataKardex[0]['SaldoCantidad'] + round($dataKardex[0]['cantidad']));
//            }
//            echo $anoInicial."-".$mesInicial."<br>";
//            echo $anoFinal."-".$mesFinal."<br>";
        $finDia = $this->getFinMes($mesFinal, $anoFinal);
        $fechaIncialFiltro = $anoInicial . "-" . $mesInicial . '-01';
        $fechaFinalFiltro = $anoFinal . "-" . $mesFinal . "-" . $finDia; //MODIFICAR LAS FECHAS.
//            echo $finDia."<br>";
//            echo $fechaIncialFiltro."<br>";
//            echo $fechaFinalFiltro."<br>";
        $fecha_inicial = strtotime(date($fechaIncialFiltro, time()));
        $fecha_final = strtotime(date($fechaFinalFiltro, time()));
//            $fecha_entrada = strtotime();
//            if($fecha_actual > $fecha_entrada){
//                echo "La fecha entrada ya ha pasado";
//            }else{
//                echo "Aun falta algun tiempo";
//            }
//            
//            $fechaFinalFiltro
        //inicio de saldo inicial:
        $canttt = 0;
        for ($i = 0; $i < $total; $i++) {
            $fechaMovvxx = explode(' ', $dataKardex[$i]['fechainicio']);
//                $fecha5 = explode('-',$fechaMovvxx[0]);
            // ||
            if (strtotime(date($fechaMovvxx[0]), time()) < $fecha_inicial) { // && strtotime(date($fechaMovvxx[0]),time()) <= $fecha_final){
                $canttt++;
//                    echo $fechaMovvxx[0]."=".$fechaIncialFiltro."=".$fecha_inicial;
//                    echo "; ".$canttt."<br>";
//                }
                if ($dataKardex[$i]['mov'] == 1) {
                    if (intval($dataKardex[$i]['compra']) == 1) {
                        $cantidadTotal += $dataKardex[$i]['cantidad'];
                        $costoUnitario = round(($costoTotal + ($dataKardex[$i]['cantidad'] * $dataKardex[$i]['costounitario'])) / $cantidadTotal, 2);
                        $costoTotal = $cantidadTotal * $costoUnitario;
                        $cantidadEntrada += $dataKardex[$i]['cantidad'];
                        $costoTotalEntrada += ($cantidadTotal * $costoUnitario);
                    } else {
                        $cantidadTotal += $dataKardex[$i]['cantidad'];
                        $costoTotal = $cantidadTotal * $costoUnitario;
                        $cantidadEntrada += $dataKardex[$i]['cantidad'];
                        $costoTotalEntrada += ($cantidadTotal * $dataKardex[$i]['cantidad']);
                    }
                } else {
                    $cantidadTotal -= $dataKardex[$i]['cantidad'];
                    $costoTotal = $cantidadTotal * $costoUnitario;
                    $cantidadSalida += $dataKardex[$i]['cantidad'];
                    $costoTotalSalida += ($dataKardex[$i]['cantidad'] * $costoUnitario);
                }
            } else {
                break;
            }
        }
//            echo "--".$i."--<br>";
        //fin de saldo inicial
        echo "<tr>";
        //echo "<td>".$mesInicial."/".$anoInicial.":".$mesFinal."/".$anoFinal."</td>";
        echo    "<td>N°</td>";
        echo    "<td colspan='4'>Saldo Inicial</td>";
        echo    "<td>16</td>";
        echo    "<td>" . $cantidadTotal . "</td>";
        echo    "<td>" . $costoUnitario . "</td>";
        echo    "<td>" . $costoTotal . "</td>";
        echo    "<td></td>";
        echo    "<td></td>";
        echo    "<td></td>";
        echo    "<td>" . $cantidadTotal . "</td>";
        echo    "<td>" . $costoUnitario . "</td>";
        echo    "<td>" . $costoTotal . "</td>";
        echo    "<td>ORDEN</td>";
        echo "</tr>";
        $cantidadEntrada = $cantidadTotal;
        $cantidadSalida = 0;
        $costoTotalEntrada = $costoTotal;
        $costoTotalSalida = 0;
//        }
        for ($i = $i; $i < $total; $i++) {
            $cont++;
            //Inicio calcular el saldo inicial
            //saldo inicial
            //$saldoInicial = 3000;
            //$cantidad = 100;
            //$precioUnitario = 30;
            //$saldoFinal = $cantidad*$saldoInicial;
            //Fin calcular el saldo inicial
            echo "<tr>";
            if ($dataKardex[$i]['codigotipooperacion'] == 16) {
                echo    "<td></td>";
                echo    "<td colspan='4'>Saldo Inicial</td>";
            } else {
                $fechaMovv = explode(' ', $dataKardex[$i]['fechainicio']);
                echo    "<td>" . ($cont) . "</td>";
                echo    "<td>" . $fechaMovv[0] . "</td>";
                echo    "<td>" . $dataKardex[$i]['tipodocumento'] . "</td>";
                echo    "<td>" . $dataKardex[$i]['serie'] . "</td>";
                echo    "<td>" . $dataKardex[$i]['documento'] . "</td>";
            }
            echo    "<td style='text-align:center'>" . $dataKardex[$i]['tipooperacion'] . "</td>";
            if ($dataKardex[$i]['mov'] == 1) {
                if (intval($dataKardex[$i]['compra']) == 1) {
                    echo    "<td style='text-align:center'>" . $dataKardex[$i]['cantidad'] . "</td>";
                    echo    "<td style='text-align:right'>" . $dataKardex[$i]['costounitario'] . "</td>";
                    echo    "<td style='text-align:right'>" . $dataKardex[$i]['cantidad'] * $dataKardex[$i]['costounitario'] . "</td>";
//                    echo "<td style='text-align:center'>" . $dataKardex[$i]['ORDEN'] . "</td>";
                    $cantidadTotal += $dataKardex[$i]['cantidad'];
                    $costoUnitario = round(($costoTotal + ($dataKardex[$i]['cantidad'] * $dataKardex[$i]['costounitario'])) / $cantidadTotal, 2);
                    $costoTotal = $cantidadTotal * $costoUnitario;
                    $cantidadEntrada += $dataKardex[$i]['cantidad'];
                    $costoTotalEntrada += ($cantidadTotal * $costoUnitario);
                } else {
                    echo    "<td style='text-align:center'>" . $dataKardex[$i]['cantidad'] . "</td>";
                    echo    "<td style='text-align:right'>" . $costoUnitario . "</td>";
                    echo    "<td style='text-align:right'>" . $dataKardex[$i]['cantidad'] * $costoUnitario . "</td>";
//                    echo "<td style='text-align:center'>" . $dataKardex[$i]['ORDEN'] . "</td>";
                    $cantidadTotal += $dataKardex[$i]['cantidad'];
                    $costoTotal = $cantidadTotal * $costoUnitario;

                    $cantidadEntrada += $dataKardex[$i]['cantidad'];
                    $costoTotalEntrada += ($cantidadTotal * $dataKardex[$i]['cantidad']);
                }
                echo    "<td style='text-align:center'></td>";
                echo    "<td style='text-align:right'></td>";
                echo    "<td style='text-align:right'></td>";
                echo    "<td style='text-align:center'>" . $cantidadTotal . "</td>";
                echo    "<td style='text-align:right'>" . $costoUnitario . "</td>";
                echo    "<td style='text-align:right'>" . $costoTotal . "</td>";
            } else {
                echo    "<td style='text-align:center'></td>";
                echo    "<td style='text-align:right'></td>";
                echo    "<td style='text-align:right'></td>";
                echo    "<td style='text-align:center'>" . $dataKardex[$i]['cantidad'] . "</td>";
                echo    "<td style='text-align:right'>" . $costoUnitario . "</td>";
                echo    "<td style='text-align:right'>" . $dataKardex[$i]['cantidad'] * $costoUnitario . "</td>";
                $cantidadTotal -= $dataKardex[$i]['cantidad'];
                $costoTotal = $cantidadTotal * $costoUnitario;
                $cantidadSalida += $dataKardex[$i]['cantidad'];
                $costoTotalSalida += ($dataKardex[$i]['cantidad'] * $costoUnitario);
                echo    "<td style='text-align:center'>" . $cantidadTotal . "</td>";
                echo    "<td style='text-align:right'>" . $costoUnitario . "</td>";
                echo    "<td style='text-align:right'>" . $costoTotal . "</td>";
            }
            echo    "<td style='text-align:center'>" . $dataKardex[$i]['ORDEN'] . "</td>";
            echo "</tr>";
        }
        echo "<tr>";
        echo    "<td colspan=6></td>";
        echo    "<th style='text-align:center'>" . $cantidadEntrada . "</td>";
        echo    "<td></td>";
        echo    "<th style='text-align:right'>" . $costoTotalEntrada . "</td>";
        echo    "<th style='text-align:center'>" . $cantidadSalida . "</td>";
        echo    "<td></td>";
        echo    "<th style='text-align:right'>" . $costoTotalSalida . "</td>";
        echo    "<td colspan=3></td>";
        echo "</tr>";
    }

    public function getFinMes($mes, $anio) {
        if ($mes == 2) {
            if (($anio % 4) == 0) {
                return 29;
            } else {
                return 28;
            }
        } else if ($mes == 1 || $mes == 3 || $mes == 5 || $mes == 7 || $mes == 8 || $mes == 10 || $mes == 12) {
            return 31;
        } else {
            return 30;
        }
    }

    public function buscaNumeroFactura() {
        $numero = $_REQUEST['id'];
        $movimiento = new Movimiento();
        $cantidad1 = $movimiento->buscaNumeroFactura($numero);
        $cantidad2 = $movimiento->buscaNumeroFacturaj($numero);
        //var_dump($exito);
//        if($cantidad1 == )
        if ($cantidad1 != 0) {
            echo $cantidad1;
        } else if ($cantidad2 != 0) {
            echo $cantidad2;
        } else {
            echo 0;
        }
    }

    public function buscaNumeroDevolucion() {
        $numero = $_REQUEST['id'];
        $movimiento = new Movimiento();
        $cantidad1 = $movimiento->buscaNumeroDevolucion($numero);
        $cantidad2 = $movimiento->buscaNumeroDevolucionj($numero);
        //var_dump($exito);
//        if($cantidad1 == )
        if ($cantidad1 != 0) {
            echo $cantidad1;
        } else if ($cantidad2 != 0) {
            echo $cantidad2;
        } else {
            echo 0;
        }
    }

    public function buscaNumeroBoleta() {
        $numero = $_REQUEST['id'];
        $movimiento = new Movimiento();
        $cantidad1 = $movimiento->buscaNumeroBoleta($numero);
        $cantidad2 = $movimiento->buscaNumeroBoletaj($numero);
        //var_dump($exito);
//        if($cantidad1 == )
        if ($cantidad1 != 0) {
            echo $cantidad1;
        } else if ($cantidad2 != 0) {
            echo $cantidad2;
        } else {
            echo 0;
        }
    }

//    create table wc_movimientoj(
//  id int(11) not null auto_increment primary key,
//        idProducto int not null,
//        fechainicio date,
//        tipodocumento varchar(10),
//        serie varchar(10),
//        documento varchar(20),
//        tipooperacion varchar(10),
//        cantidad int, 
//        usuariocreacion int,
//        fechacreacion datetime,
//        usuariomodificacion int,
//        fechamodificacion datetime
//    ) ENGINE=InnoDB;
    public function graba2() {
        $numero = $_POST['txtNumero'];
        $fecha = $_POST['txtFechaFactura'];
        $tipoDoc = $_POST['txttipoDoc'];
        $serie = $_POST['txtSerie'];
        $tipoMov = $_POST['txttipoMov'];
        $mov = $_POST['txtMov'];
        $tipoDocumento = $_POST['txtTipoDocument'];
        $idProducto = $_POST['txtIdProducto'];
        $cantidad = $_POST['txtCantidadHecha'];
        $data['documento'] = $numero;
        $data['fechainicio'] = $fecha;
        $data['tipodocumento'] = $tipoDoc;
        $data['serie'] = $serie;
        $data['tipooperacion'] = $tipoMov;
        $data['mov'] = $mov;
        $data['document'] = $tipoDocumento;
        $movimiento = new Movimiento();
        //echo $numero."-".$fecha."-".$tipoDoc."-".$tipoMov."-".$idProducto[0]."-".$cantidad[0];
        $contador = count($idProducto);
        for ($i = 0; $i < $contador; $i++) {
            $data['idProducto'] = $idProducto[$i];
            $data['cantidad'] = $cantidad[$i];

            $exito = $movimiento->grabaFacturaj($data);
        }
        //$this->JIngresoMovimiento();
        if ($tipoDocumento == 1) {
            $ruta['ruta'] = "/movimiento/jingresomovimiento";
            $this->view->show("ruteador.phtml", $ruta);
        } else if ($tipoDocumento == 2) {
            $ruta['ruta'] = "/movimiento/jingresarDevolucion";
            $this->view->show("ruteador.phtml", $ruta);
        } else { //if($tipoDocumento == 3){
            $ruta['ruta'] = "/movimiento/jingresarBoleta";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function ingresoSaldosIniciales() {
        $this->view->show('/movimiento/ingresoSaldosIniciales.phtml', $data);
    }

    function verficarSaldoInicial() {
        $idProducto = $_REQUEST['id'];
        $movimiento = new Movimiento();
        $data = $movimiento->buscarSaldoInicialj($idProducto);
        echo $data;
    }

    function grabaSaldosIniciales() {
        $idProducto = $_POST['txtIdProducto'];
        $cantidad = $_POST['txtCantidadHecha'];
        $precioinicial = $_POST['txtPrecioInicial'];
        $data['tipooperacion'] = 16;
        $data['mov'] = 1;
        $data['document'] = $tipoDocumento;
        $movimiento = new Movimiento();
        //echo $numero."-".$fecha."-".$tipoDoc."-".$tipoMov."-".$idProducto[0]."-".$cantidad[0];
        $contador = count($idProducto);
//        echo $contador;
        for ($i = 0; $i < $contador; $i++) {
            $data['idProducto'] = $idProducto[$i];
            $data['cantidad'] = $cantidad[$i];
            $data['costoinicial'] = $precioinicial[$i];
            //var_dump($data);
            $exito = $movimiento->grabaSaldosIniciales($data);
        }
        $ruta['ruta'] = "/movimiento/ingresoSaldosIniciales";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function inventariopermanentevalorizado() {
        $data['mes'] = $this->meses();
        $this->view->show("/movimiento/inventariopermanentevalorizado.phtml", $data);
    }

    function simulacroipv() {
        $data['mes'] = $this->meses();
        $this->view->show("/movimiento/simulacroipv.phtml", $data);
    }
    
    function simulacroinventariovalorizado() {
        set_time_limit(1500);
        if (!empty($_REQUEST['txtidsaldoinicial'])) {
            $idsaldoinicial = explode(":", $_REQUEST['txtidsaldoinicial']);
            $idproducto = $_REQUEST['txtidproducto'];
            $idsaldoinicial = $idsaldoinicial[0];
            $txtmesfinal = $_REQUEST['txtmesfinal'];
            $saldoinicialmodel = new saldosIniciales();
            $dataSaldoInicial = $saldoinicialmodel->verSaldonicialgeneral($idsaldoinicial);
            if (count($dataSaldoInicial) == 1 && $dataSaldoInicial[0]['idproducto'] == $idproducto) {
                echo "<center><b>SIMULACRO: Registro de Inventario Permanente Valorizado</b></center> <br>";

                $mes = $this->meses();
                $tempFechaPeriodo = explode("-", $dataSaldoInicial[0]['fechasaldo']);
                $mesFinal = $tempFechaPeriodo[0] . "-" . str_pad($txtmesfinal, 2, "0", STR_PAD_LEFT) . "-" . date("d", (mktime(0, 0, 0, $txtmesfinal + 1, 1, $tempFechaPeriodo[0]) - 1));
                $tempFechaPeriodo = $mes[$tempFechaPeriodo[1] * 1] . " A " . $mes[$txtmesfinal] . " DEL " . $tempFechaPeriodo[0];
                
                echo '<table>';
                echo    '<tr>';
                echo        '<td><b>PERIODO: </b></td>';
                echo        '<td>' . strtoupper($tempFechaPeriodo) . '</td>';
                echo    '</tr>';
                echo    '<tr>';
                echo        '<td><b>RAZON SOCIAL: </b></td>';
                echo        '<td>CORPORACION POWER ACOUSTIK S.A.C.</td>';
                echo    '</tr>';
                echo    '<tr>';
                echo        '<td><b>RUC: </b></td>';
                echo        '<td>20509811858</td>';
                echo    '</tr>';
                echo    '<tr>';
                echo        '<td><b>ESTABLECIMIENTO: </b></td>';
                echo        '<td>Almacén General</td>';
                echo    '</tr>';
                echo    '<tr>';
                echo        '<td><b>CODIGO DE LA EXISTENCIA: </b></td>';
                echo        '<td>' . $dataSaldoInicial[0]['codigopa'] .'</td>';
                echo    '</tr>';
                echo    '<tr>';
                echo        '<td><b>TIPO: </b></td>';
                echo        '<td>Mercaderías</td>';
                echo    '</tr>';
                echo    '<tr>';
                echo        '<td><b>DESCRIPCION: </b></td>';
                echo        '<td>' . $dataSaldoInicial[0]['nompro'] .'</td>';
                echo    '</tr>';
                echo    '<tr>';
                echo        '<td><b>UNIDAD DE MEDIDA: </b></td>';
                echo        '<td>UNIDADES</td>';
                echo    '</tr>';
                echo    '<tr>';
                echo        '<td><b>METODO DE EVALUACION: </b></td>';
                echo        '<td>Promedio Móvil</td>';
                echo    '</tr>';
                echo '</table>';
                echo '<br>';
                
                echo '<table>';
                echo    '<thead>';
                echo        '<tr>';
                echo            '<th rowspan="2">FECHA</th>';
                echo            '<th rowspan="2">TIPO DOC.</th>';
                echo            '<th rowspan="2">SERIE</th>';
                echo            '<th rowspan="2">NUMERO</th>';
                echo            '<th rowspan="2">TIPO MOV.</th>';
                echo            '<th colspan="3">ENTRADAS</th>';
                echo            '<th colspan="3">SALIDAS</th>';
                echo            '<th colspan="3">SALDO FINAL</th>';
                echo        '</tr>';
                echo        '<tr>';
                echo            '<th>Cant.</th>';
                echo            '<th>Cost. Unit S/.</th>';
                echo            '<th>Costo total S/.</th>';
                echo            '<th>Cant.</th>';
                echo            '<th>Cost. Unit S/.</th>';
                echo            '<th>Costo total S/.</th>';
                echo            '<th>Cant.</th>';
                echo            '<th>Cost. Unit S/.</th>';
                echo            '<th>Costo total S/.</th>';
                echo        '</tr>';
                echo    '</thead>';
                echo    '<tbody>';
                $inventariomodel = new inventario();
                $dataInventario = $inventariomodel->listaKardexValorizado($idproducto, $dataSaldoInicial[0]['fechasaldo'], $mesFinal);
                echo        '<tr>';
                echo            '<td style="text-align:center">' . str_replace('-', '.', $dataSaldoInicial[0]['fechasaldo']) . '</td>';
                echo            '<td style="text-align:center" colspan="3">SALDO INICIAL</td>';
                echo            '<td style="text-align:center">16</td>';
                echo            '<td style="text-align:right">' .  $dataSaldoInicial[0]['cantidad1'] . '</td>';
                echo            '<td style="text-align:right">' . number_format($dataSaldoInicial[0]['costounitario'], 2) . '</td>';
                echo            '<td style="text-align:right">' . number_format(($dataSaldoInicial[0]['cantidad1'] * $dataSaldoInicial[0]['costounitario']), 2) . '</td>';
                echo            '<td></td>';
                echo            '<td></td>';
                echo            '<td></td>';
                echo            '<td style="text-align:right">' . $dataSaldoInicial[0]['cantidad1'] . '</td>';
                echo            '<td style="text-align:right">' . number_format($dataSaldoInicial[0]['costounitario'], 2) . '</td>';
                echo            '<td style="text-align:right">' . number_format($dataSaldoInicial[0]['cantidad1'] * $dataSaldoInicial[0]['costounitario'], 2) . '</td>';
                echo        '</tr>';
                
                $stockActual = $dataSaldoInicial[0]['cantidad1'];
                $precioglobal = number_format($dataSaldoInicial[0]['costounitario'], 2, '.', '');
                $cantidadEntradas = $dataSaldoInicial[0]['cantidad1'];
                $totalEntrada = number_format(($stockActual * $precioglobal), 2, '.', '');
                $costototalanterior = $totalEntrada;
                $cantidadSalida = 0;
                $totalSalida = 0;
                $tamanio = count($dataInventario);
                $arrayListTipocambio = array();
                $tipocambiomodel = new TipoCambio();
                for ($i = 0; $i < $tamanio; $i++) {
                    echo        '<tr>';
                    echo            '<td style="text-align:center">' . str_replace('-', '.', $dataInventario[$i]['fecha']) . '</td>';
                    echo            '<td style="text-align:center">0' . $dataInventario[$i]['operacion'] . '</td>';
                    
                    if ($dataInventario[$i]['tipomovimiento'] * 1 == 2) {
                        echo            '<td style="text-align:center">' . $dataInventario[$i]['serie'] . '</td>';
                        echo            '<td style="text-align:center">' . $dataInventario[$i]['numdoc'] . '</td>';
                    } else {
                        if ($dataInventario[$i]['electronico'] == 0) {
                            $ncomprobante = $dataInventario[$i]['numdoc'];
                            $tempComprobantes = $inventariomodel->arrayComprobantesCorrectos($ncomprobante);
                            $listaDetalleOrdenVenta = $inventariomodel->listaDetalleOrdenVenta($dataInventario[$i]["idordenventa"]);
                            $hasta = count($listaDetalleOrdenVenta);
                            for ($j = 0; $j < $hasta; $j++) {
                                if ($dataInventario[$i]["idproducto"] == $listaDetalleOrdenVenta[$j]['idproducto']) {
                                    if (isset($tempComprobantes[intval(($j) / 35)]["numdoc"])) {
                                        $numdoc = $tempComprobantes[intval(($j) / 35)]["numdoc"];
                                    } else {
                                        $numdoc = $tempComprobantes[intval(($j) / 35) - 1]["numdoc"];
                                    }
                                    $j = $hasta;
                                }
                            }
                            echo            '<td style="text-align:center">' . str_pad($dataInventario[$i]['serie'], 3, "0", STR_PAD_LEFT) . '</td>';
                            echo            '<td style="text-align:center">' . $numdoc . '</td>';
                        } else {
                            $listaComprobantesElectronicas = $inventariomodel->listaComprobantesElectronicas($dataInventario[$i]["idordenventa"]);
                            $listaDetalleOrdenVenta = $inventariomodel->listaDetalleOrdenVenta($dataInventario[$i]["idordenventa"]);
                            $hasta = count($listaDetalleOrdenVenta);
                            for ($j = 0; $j < $hasta; $j++) {
                                if ($dataInventario[$i]["idproducto"] == $listaDetalleOrdenVenta[$j]['idproducto']) {
                                    foreach ($listaComprobantesElectronicas as $valor) {
                                        if (($j + 1) >= $valor['desde'] and ( $j + 1) <= $valor['hasta']) {
                                            $numdoc = $valor['numdoc'];
                                        }
                                    }
                                    $j = $hasta;
                                }
                            }
                            echo            '<td style="text-align:center">' . ($dataInventario[$i]['nombredoc'] == 2 ? 'B' : 'F') . str_pad($dataInventario[$i]['serie'], 3, "0", STR_PAD_LEFT) . '</td>';
                            echo            '<td style="text-align:center">' . str_pad($numdoc, 8, "0", STR_PAD_LEFT) . '</td>';
                        }
                    }
                    echo            '<td style="text-align:center">' .  $dataInventario[$i]['tipomovimiento'] . '</td>';
                    if ($dataInventario[$i]['tipomovimiento'] * 1 == 1) {
                        $stockActual -= $dataInventario[$i]['cantidad'];
                        $cantidadSalida += $dataInventario[$i]['cantidad'];
                        $totalSalida += number_format(($dataInventario[$i]['cantidad'] * $precioglobal), 2, '.', '');
                        echo            '<td></td>';
                        echo            '<td></td>';
                        echo            '<td></td>';
                        echo            '<td style="text-align:right">' . $dataInventario[$i]['cantidad'] . '</td>';
                        echo            '<td style="text-align:right">' . number_format($precioglobal, 2) . '</td>';
                        echo            '<td style="text-align:right">' . number_format(($dataInventario[$i]['cantidad'] * $precioglobal), 2) . '</td>';
                    } else {
                        $tipocambiodelmes = 1;
                        if ($dataInventario[$i]['idmoneda'] == 2) {
                            $tempFechaIni = explode("-", $dataInventario[$i]['fecha']);
                            if (!isset($arrayListTipocambio[$tempFechaIni[0] . "-" . $tempFechaIni[1]])) {
                                $tempFechaFin = $tempFechaIni[0] . "-" . $tempFechaIni[1] . "-" . date("d", (mktime(0, 0, 0, $tempFechaIni[1] + 1, 1, $tempFechaIni[0]) - 1));
                                $tempFechaIni = $tempFechaIni[0] . "-" . $tempFechaIni[1] . "-01";
                                $tipocambiodelmes = $tipocambiomodel->ultimotipodecambiodelmes($tempFechaIni, $tempFechaFin);
                                $arrayListTipocambio[$tempFechaIni[0] . "-" . $tempFechaIni[1]] = $tipocambiodelmes;
                            } else {
                                $tipocambiodelmes = $arrayListTipocambio[$tempFechaIni[0] . "-" . $tempFechaIni[1]];
                            }
                        }
                        $dataInventario[$i]['costounitario'] = number_format(($dataInventario[$i]['costounitario'] * $tipocambiodelmes), 2, '.', '');
                        $costototalactual = number_format(($dataInventario[$i]['costounitario'] * $dataInventario[$i]['cantidad']), 2, '.', '');
                        $stockActual += $dataInventario[$i]['cantidad'];
                        $cantidadEntradas += $dataInventario[$i]['cantidad'];
                        $costototalanterior += $costototalactual;
                        $precioglobal = $costototalanterior / $stockActual;
                        echo            '<td style="text-align:right">' . $dataInventario[$i]['cantidad'] . '</td>';
                        echo            '<td style="text-align:right">' . number_format(($dataInventario[$i]['costounitario']), 2) . '</td>';
                        echo            '<td style="text-align:right">' . number_format($costototalactual, 2) . '</td>';
                        echo            '<td></td>';
                        echo            '<td></td>';
                        echo            '<td></td>';
                        $totalEntrada += number_format($costototalactual, 2, '.', '');
                    }
                    echo            '<td style="text-align:right">' . $stockActual . '</td>';
                    echo            '<td style="text-align:right">' . number_format($precioglobal, 2) . '</td>';
                    echo            '<td style="text-align:right">' . number_format(($stockActual * $precioglobal), 2) . '</td>';
                    $costototalanterior = number_format(($stockActual * $precioglobal), 2, '.', '');
                    echo        '</tr>';
                }
                echo        '<tr>';
                echo            '<td colspan="4"></td>';
                echo            '<th>TOTALES</th>';
                echo            '<td style="text-align:right"><b>' . $cantidadEntradas . '</b></td>';
                echo            '<td></td>';
                echo            '<td style="text-align:right"><b>' . number_format($totalEntrada, 2) . '</b></td>';
                echo            '<td style="text-align:right"><b>' . $cantidadSalida . '</b></td>';
                echo            '<td></td>';
                echo            '<td style="text-align:right"><b>' . number_format($totalSalida, 2) . '</b></td>';
                echo        '</tr>';
                echo    '</tbody>';
                echo '</table>';
                $nuevafecha = strtotime('+1 day', strtotime($mesFinal));
                $nuevafecha = date('Y-m-d', $nuevafecha);
                $saldosIniciales = $this->AutoLoadModel('saldosIniciales');
                $evaluarDuplicidad = $saldosIniciales->evaluarDuplicididad($idproducto, $nuevafecha, 1);
                if (count($evaluarDuplicidad) == 0) {
                    $grabosaldoinicial = $saldosIniciales->insertarSaldoInicial($idproducto, $stockActual, $precioglobal, '', 1, 2, $nuevafecha, $_SESSION['idactor'], date("Y-m-d H:i:s"), 1);
                } else {
                    $grabosaldoinicial = $saldosIniciales->actualizarSaldoInicial($evaluarDuplicidad[0]['idsaldo'], $stockActual, $precioglobal, '', $_SESSION['idactor'], date("Y-m-d H:i:s"));
                }
            } else {
                echo "Los Parametros Ingresados No Son Correctos.";
            }
        } else {
            echo "Saldo No Seleccionado.";
        }    
    }

    function verificarsaldosiniciales() {
        $idProducto = $_POST['idproducto'];
        $simulacro = $_POST['simulacro'];
        $tempPeriodo = "<option value=''> --- Desde --- </option>";
        $data['rspta'] = 0;
        $saldoinicial = new saldosIniciales();
        if ($simulacro == 1) {
            $lstSaldos = $saldoinicial->listaSaldosInicialesxProductoinicial($idProducto);
        } else {
            $lstSaldos = $saldoinicial->listaSaldosInicialesxProducto($idProducto);
        }
        $tam = count($lstSaldos);
        if ($tam > 0) {
            $arrayMeses = $this->meses();
            for ($i = 0; $i < $tam; $i++) {
                $tempFecha = explode("-", $lstSaldos[$i]['fechasaldo']);
                $tempPeriodo .= "<option value='" . $lstSaldos[$i]['idsaldo'] . ":" . ($tempFecha[1] * 1) . "'>" . $arrayMeses[$tempFecha[1] * 1] . " - " . $tempFecha[0] . "</option>";
            }
            if ($simulacro == 1) {
                $lstSaldos = $saldoinicial->listaSaldosInicialesxProducto($idProducto, 1);
                $tam = count($lstSaldos);
                for ($i = 0; $i < $tam; $i++) {
                    $tempFecha = explode("-", $lstSaldos[$i]['fechasaldo']);
                    $tempPeriodo .= "<option value='" . $lstSaldos[$i]['idsaldo'] . ":" . ($tempFecha[1] * 1) . "'>" . $arrayMeses[$tempFecha[1] * 1] . " - " . $tempFecha[0] . "</option>";
                }
            }
            $data['rspta'] = 1;
        }
        $data['periodo'] = $tempPeriodo;
        echo json_encode($data);
    }

    public function ejecutar() {
        set_time_limit(1000);
        echo 'INICIO DE METODO EJECUTAR <br><br>';
        $codigosproducto = array("  ZR-11   ", 
                                "   ZR-28   ", 
                                "   D-R5    ", 
                                "   HT-336H ", 
                                "   SY-035  ", 
                                "   CT-7662 ", 
                                "   ES-210MXBLU ", 
                                "   IPOP152AK   ", 
                                "   LAM-40102   ", 
                                "   LAM-41200   ", 
                                "   SR-2231 ", 
                                "   LK269   ", 
                                "   WT-360  ", 
                                "   XYK-56  ", 
                                "   WT-3570 ", 
                                "   XYA-07  ", 
                                "   XYA-06  ", 
                                "   CT-2698.    ", 
                                "   MS-0209 PR  ", 
                                "   MS-0209 GOLD    ", 
                                "   CT-1039-5FT BL  ", 
                                "   CT-1039-5FT-BK  ", 
                                "   CT-1040-5FT BK  ", 
                                "   EXT-ST-1.8MT    ", 
                                "   CT-1039-3FT ", 
                                "   N-251   ", 
                                "   EXT-ST-3MT  ", 
                                "   EXT-ST-5MT  ", 
                                "   CT-12V5A    ", 
                                "   CT-12V10A   ", 
                                "   CT-12V15A   ", 
                                "   CT-12V20A   ", 
                                "   XW-60W  ", 
                                "   XW-120W ", 
                                "   HT-010-20A  ", 
                                "   SPCAT5E-5 GY    ", 
                                "   CT-4154 ", 
                                "   WD2031 (BK) ", 
                                "   WD2031 (BL) ", 
                                "   RM301   ", 
                                "   RM501   ", 
                                "   P7002AP-4   ", 
                                "   P7003AP-2   ", 
                                "   HT-010 10A  ", 
                                "   HT-010 50A  ", 
                                "   IRS-201-1B3-R/B ", 
                                "   IRS-201-1B3-G/B ", 
                                "   IRS-203-1C6-G/B ", 
                                "   IRS-201-1B3-B/B ", 
                                "   IRS-203-1C6-B/B ", 
                                "   AFW-L10RF-GR-100    ", 
                                "   DL5MM-BL-100    ", 
                                "   SP-DL5MM-BL-100 ", 
                                "   SP-DL5MM-GR-100 ", 
                                "   DL5MM-QF-1-100  ", 
                                "   2SA 1943    ", 
                                "   2SB 817 ", 
                                "   MJ 15004G   ", 
                                "   YH-3643 ", 
                                "   CT-2051 ", 
                                "   TC-6X6X4.1-1104AM   ", 
                                "   C-5299  ", 
                                "   D-5036  ", 
                                "   C-5297  ", 
                                "   C-5287  ", 
                                "   8873C5CNG6UP3   ", 
                                "   8895C5NG7FU7    ", 
                                "   KA-3525A    ", 
                                "   KA-7500B    ", 
                                "   KA-2284 ", 
                                "   M65831AP    ", 
                                "   CD-2003GP   ", 
                                "   NE-5532P    ", 
                                "   TDA-72665A  ", 
                                "   CW-24C08C   ", 
                                "   CD-4053BE   ", 
                                "   11105-K010A ", 
                                "   D-5038  ", 
                                "   GC-101  ", 
                                "   XJWJ-50 ", 
                                "   SRT-11  ", 
                                "   SRT-001B1   ", 
                                "   PCYT-1,8    ", 
                                "   PCYT-3,0    ", 
                                "   VG-7.1  ", 
                                "   Z07-5 PINK  ", 
                                "   Z07-5 BL    ", 
                                "   GC-107-C    ", 
                                "   KD1-6P4CIV  ", 
                                "   GC-103  ", 
                                "   CPM-3   ", 
                                "   SUT-966 ", 
                                "   JDW-20A ", 
                                "   HQ 10AWG BL ", 
                                "   HQ 10AWG GR ", 
                                "   SUT-5600A   ", 
                                "   HQ12AWG BROWN   ", 
                                "   HQ12AWG GR  ", 
                                "   HQ12AWG WH  ", 
                                "   CPM-6   ", 
                                "   CMV-14  ", 
                                "   SUT-508 B   ", 
                                "   APS45C  ", 
                                "   R-1675  ", 
                                "   CMV-12  ", 
                                "   MAR-1   ", 
                                "   MC-5574 ", 
                                "   MC 1001 RD  ", 
                                "   LT069 BL    ", 
                                "   LT069 7 COLORS  ", 
                                "   SUT-069A 7C ", 
                                "   SUT-C375    ", 
                                "   SUT-1800 AM ", 
                                "   SUT-1327CL  ", 
                                "   SUT30W  ", 
                                "   SUT-338 WH  ", 
                                "   SUT-339 WH  ", 
                                "   SUT-09-H11  ", 
                                "   SUT-09-H13  ", 
                                "   SUT-L2419   ", 
                                "   MC410   ", 
                                "   H3  ", 
                                "   SUT-6405    ", 
                                "   SUT-016 BK  ", 
                                "   ES7202N ", 
                                "   CRX667814 A ", 
                                "   SUT-67302   ", 
                                "   SUT-67267   ", 
                                "   SUT-6190    ", 
                                "   SUT-67031   ", 
                                "   SUT-T16 ", 
                                "   SUT-OW6 ", 
                                "   SUT-VW2 ", 
                                "   SUT-124 ", 
                                "   SUT-1382A   ", 
                                "   B-RED   ", 
                                "   B-BLUE  ", 
                                "   SUT-327-2   ", 
                                "   SUT-327-3   ", 
                                "   SUT-1032    ", 
                                "   SUT-6408    ", 
                                "   SUT-6393    ", 
                                "   SUT-234 ", 
                                "   SUT-8020BB  ", 
                                "   SUT-6406    ", 
                                "   EM30-003    ", 
                                "   SUT29-038   ", 
                                "   SUT29-016   ", 
                                "   SUT-EM29077 ", 
                                "   SUT-39001   ", 
                                "   MC38007 37  ", 
                                "   MC38007 38  ", 
                                "   MC39040 ", 
                                "   HVC10000E   ", 
                                "   HVC2500 ", 
                                "   HVC10000SE-2    ", 
                                "   AR-2500 ", 
                                "   AR-3500 ", 
                                "   HVC2V78F    ", 
                                "   MP-6500 ", 
                                "   MP-6500R    ", 
                                "   OPCQF1.2    ", 
                                "   OS-MVS25    ", 
                                "   KMC-2GFCL   ", 
                                "   KMC-2GFCLE  ", 
                                "   KMC-3GFCL   ", 
                                "   HVC12-2E    ", 
                                "   6206    ", 
                                "   DRK-VP50    ", 
                                "   MCD4H   ", 
                                "   MCB-12  ", 
                                "   MCB-14  ", 
                                "   MCB-16  ", 
                                "   DRKVP38-YW  ", 
                                "   WT406   ", 
                                "   WT413   ", 
                                "   DRKVP25-YW  ", 
                                "   12B-R   ", 
                                "   ZR-8    ", 
                                "   MHQ-4C  ", 
                                "   MHQ-6C  ", 
                                "   EJE ", 
                                "   SCO60336    ", 
                                "   SUT-5289 WHITE  ", 
                                "   SUT-5289 RD ", 
                                "   100MM(L,A)  "
                                );
        $valorcodigoSunat = array(" 21101801    ", 
"   21101801    ", 
"   21101801    ", 
"   27115121    ", 
"   27112719    ", 
"   39121616    ", 
"   52161527    ", 
"   52161527    ", 
"   45111618    ", 
"   45111618    ", 
"   52161547    ", 
"   52161610    ", 
"   52161611    ", 
"   52161610    ", 
"   52161611    ", 
"   52161611    ", 
"   52161611    ", 
"   60104912    ", 
"   26121539    ", 
"   26121539    ", 
"   26121539    ", 
"   26121539    ", 
"   26121539    ", 
"   26121539    ", 
"   26121539    ", 
"   26121539    ", 
"   26121539    ", 
"   26121539    ", 
"   43201556    ", 
"   43201556    ", 
"   43201556    ", 
"   43201556    ", 
"   43201556    ", 
"   43201556    ", 
"   26121507    ", 
"   26121641    ", 
"   26121641    ", 
"   31163100    ", 
"   31163100    ", 
"   31163100    ", 
"   31163100    ", 
"   31163100    ", 
"   31163100    ", 
"   26131507    ", 
"   26131507    ", 
"   40101600    ", 
"   40101600    ", 
"   40101600    ", 
"   40101600    ", 
"   40101600    ", 
"   32131000    ", 
"   32131000    ", 
"   32131000    ", 
"   32131000    ", 
"   32131000    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41113707    ", 
"   41106400    ", 
"   41106400    ", 
"   45111714    ", 
"   43201556    ", 
"   32131000    ", 
"   32131000    ", 
"   43201556    ", 
"   56101712    ", 
"   56101712    ", 
"   43222803    ", 
"   43201556    ", 
"   43222803    ", 
"   26121539    ", 
"   43201556    ", 
"   39121013    ", 
"   60104912    ", 
"   60104912    ", 
"   60104912    ", 
"   60104912    ", 
"   60104912    ", 
"   41114519    ", 
"   26121539    ", 
"   26121539    ", 
"   31162407    ", 
"   46171602    ", 
"   30103206    ", 
"   26121539    ", 
"   30103206    ", 
"   46171500    ", 
"   25174211    ", 
"   02517231    ", 
"   02517231    ", 
"   25172301    ", 
"   02517291    ", 
"   02517291    ", 
"   02517291    ", 
"   39101612    ", 
"   39101612    ", 
"   39101612    ", 
"   39101612    ", 
"   39101612    ", 
"   39101612    ", 
"   39101612    ", 
"   39101612    ", 
"   41111970    ", 
"   25172604    ", 
"   25172604    ", 
"   06012012    ", 
"   06012012    ", 
"   06012012    ", 
"   06012012    ", 
"   06012012    ", 
"   25174211    ", 
"   25174211    ", 
"   25174211    ", 
"   40142501    ", 
"   46121604    ", 
"   46121604    ", 
"   46121604    ", 
"   25191719    ", 
"   25191719    ", 
"   43202106    ", 
"   41111970    ", 
"   41111970    ", 
"   41111970    ", 
"   41111970    ", 
"   41111970    ", 
"   40101600    ", 
"   40101600    ", 
"   40101600    ", 
"   40101600    ", 
"   40101600    ", 
"   02517245    ", 
"   02517245    ", 
"   02517245    ", 
"   41115323    ", 
"   41115323    ", 
"   41115323    ", 
"   41115323    ", 
"   41115323    ", 
"   41115323    ", 
"   41115323    ", 
"   41115323    ", 
"   41115323    ", 
"   41115323    ", 
"   41115323    ", 
"   41115323    ", 
"   41115323    ", 
"   30191800    ", 
"   30191800    ", 
"   30191800    ", 
"   30191800    ", 
"   30191800    ", 
"   30191800    ", 
"   30191800    ", 
"   30191800    ", 
"   26101710    ", 
"   26101710    ", 
"   26101710    ", 
"   30191800    ", 
"   30191800    ", 
"   30201600    ", 
"   30201600    ", 
"   02611512    ", 
"   31162402    ", 
"   31341200    ", 
"   31341200    ", 
"   44102304    "
);
        $tamanio = count($codigosproducto);
        echo 'Tamanio codigo: ' . $tamanio . ' - Tamaño de codigo sunat: ' . count($valorcodigoSunat) . '<br><br>';
        $producto = new producto();
        for ($i = 0; $i < $tamanio; $i++) {
            $tempCodigo = trim($codigosproducto[$i]);
            $dataProducto = $producto->buscaxcodigo($tempCodigo);
            $valorproductolimpio = trim($valorcodigoSunat[$i]);
            echo '[' . $i . '] ' . $tempCodigo . ' => ' . $dataProducto[0]['codigopa'] . ': ' . $valorproductolimpio . ' ---> ';
            if ($tempCodigo == $dataProducto[0]['codigopa']) {
                $dataActualizado['codigosunat'] = $valorproductolimpio;
                $producto->actualizaProducto($dataActualizado, $dataProducto[0]['idproducto']);
                echo 'ACTUALIZADO <br>';
            } else {
                echo 'SIN ACTUALIZAR <br>';
            }
        }
    }

}

?>