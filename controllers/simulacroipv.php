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

    function detalle() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = $_REQUEST['idcontenedor'];
        }
        $detalleMovimiento = new Detallemovimiento();
        $data = $detalleMovimiento->buscaDetalleMovimiento($id);
        for ($i = 0; $i < count($data); $i++) {
            echo "<tr>";
            echo "<td>" . $data[$i]['codigopa'] . "</td>";
            echo "<td>" . $data[$i]['nompro'] . "</td>";
            echo "<td>" . $data[$i]['observaciones'] . "</td>";
            echo "<td>" . $data[$i]['cantidad'] . "</td>";
            echo "</tr>";
        }
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
            echo "<td></td>";
            echo "<td colspan='4'>Saldo Inicial</td>";
            echo "<td>16</td>";
            echo "<td>" . $cantidad . "</td>";
            echo "<td>" . $dataKardex[0]['SaldoPrecio'] . "</td>";
            echo "<td>" . round($dataKardex[0]['SaldoPrecio'] * $cantidad, 2) . "</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td>" . $cantidad . "</td>";
            echo "<td>" . $dataKardex[0]['SaldoPrecio'] . "</td>";
            echo "<td>" . round($dataKardex[0]['SaldoPrecio'] * $cantidad, 2) . "</td>";
            echo "</tr>";
        }

        for ($i = 0; $i < $total; $i++) {
            echo "<tr>";
            if ($dataKardex[$i]['codigotipooperacion'] == 16) {
                echo "<td></td>";
                echo "<td colspan='4'>Saldo Inicial</td>";
            } else {
                $cont++;
                echo "<td>" . ($cont) . "</td>";
                echo "<td>" . $dataKardex[$i]['fechamovimiento'] . "</td>";
                echo "<td>" . $dataKardex[$i]['codigotipodocumento'] . "</td>";
                echo "<td>" . $dataKardex[$i]['serie'] . "</td>";
                echo "<td>" . $dataKardex[$i]['ndocumento'] . "</td>";
            }

            echo "<td style='text-align:center'>" . $dataKardex[$i]['codigotipooperacion'] . "</td>";
            echo "<td style='text-align:center'>" . $dataKardex[$i]['EntradaCantidad'] . "</td>";
            echo "<td style='text-align:right'>" . (empty($dataKardex[$i]['EntradaPrecio']) ? '' : number_format($dataKardex[$i]['EntradaPrecio'], 2)) . "</td>";
            echo "<td style='text-align:right'>" . (empty($dataKardex[$i]['EntradaCosto']) ? '' : number_format($dataKardex[$i]['EntradaCosto'], 2)) . "</td>";
            echo "<td style='text-align:center'>" . $dataKardex[$i]['SalidaCantidad'] . "</td>";
            echo "<td style='text-align:right'>" . (empty($dataKardex[$i]['SalidaPrecio']) ? '' : number_format($dataKardex[$i]['SalidaPrecio'], 2)) . "</td>";
            echo "<td style='text-align:right'>" . (empty($dataKardex[$i]['SalidaCosto']) ? '' : number_format($dataKardex[$i]['SalidaCosto'], 2)) . "</td>";
            echo "<td style='text-align:center'>" . round($dataKardex[$i]['SaldoCantidad']) . "</td>";
            echo "<td style='text-align:right'>" . (empty($dataKardex[$i]['SaldoPrecio']) ? '' : number_format($dataKardex[$i]['SaldoPrecio'], 2)) . "</td>";
            echo "<td style='text-align:right'>" . (empty($dataKardex[$i]['SaldoCosto']) ? '' : number_format($dataKardex[$i]['SaldoCosto'], 2)) . "</td>";
            echo "</tr>";
            $tecant += $dataKardex[$i]['EntradaCantidad'];
            $tecosto += $dataKardex[$i]['EntradaCosto'];
            $tscant += $dataKardex[$i]['SalidaCantidad'];
            $tscosto += $dataKardex[$i]['SalidaCosto'];
        }
        echo "<tr>";
        echo "<td colspan=6></td>";
        echo "<th style='text-align:center'>" . round($tecant) . "</td>";
        echo "<td></td>";
        echo "<th style='text-align:right'>" . number_format($tecosto, 2) . "</td>";
        echo "<th style='text-align:center'>" . round($tscant) . "</td>";
        echo "<td></td>";
        echo "<th style='text-align:right'>" . number_format($tscosto, 2) . "</td>";
        echo "<td colspan=3></td>";
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
//            echo "tmr";
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
        echo "<td>N°</td>";
        echo "<td colspan='4'>Saldo Inicial</td>";
        echo "<td>16</td>";
        echo "<td>" . $cantidadTotal . "</td>";
        echo "<td>" . $costoUnitario . "</td>";
        echo "<td>" . $costoTotal . "</td>";
        echo "<td></td>";
        echo "<td></td>";
        echo "<td></td>";
        echo "<td>" . $cantidadTotal . "</td>";
        echo "<td>" . $costoUnitario . "</td>";
        echo "<td>" . $costoTotal . "</td>";
        echo "<td>ORDEN</td>";
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
                echo "<td></td>";
                echo "<td colspan='4'>Saldo Inicial</td>";
            } else {
                $fechaMovv = explode(' ', $dataKardex[$i]['fechainicio']);

                echo "<td>" . ($cont) . "</td>";
                echo "<td>" . $fechaMovv[0] . "</td>";
                echo "<td>" . $dataKardex[$i]['tipodocumento'] . "</td>";
                echo "<td>" . $dataKardex[$i]['serie'] . "</td>";
                echo "<td>" . $dataKardex[$i]['documento'] . "</td>";
            }

            echo "<td style='text-align:center'>" . $dataKardex[$i]['tipooperacion'] . "</td>";

            if ($dataKardex[$i]['mov'] == 1) {
                if (intval($dataKardex[$i]['compra']) == 1) {
                    echo "<td style='text-align:center'>" . $dataKardex[$i]['cantidad'] . "</td>";
                    echo "<td style='text-align:right'>" . $dataKardex[$i]['costounitario'] . "</td>";
                    echo "<td style='text-align:right'>" . $dataKardex[$i]['cantidad'] * $dataKardex[$i]['costounitario'] . "</td>";
//                    echo "<td style='text-align:center'>" . $dataKardex[$i]['ORDEN'] . "</td>";

                    $cantidadTotal += $dataKardex[$i]['cantidad'];
                    $costoUnitario = round(($costoTotal + ($dataKardex[$i]['cantidad'] * $dataKardex[$i]['costounitario'])) / $cantidadTotal, 2);
                    $costoTotal = $cantidadTotal * $costoUnitario;

                    $cantidadEntrada += $dataKardex[$i]['cantidad'];
                    $costoTotalEntrada += ($cantidadTotal * $costoUnitario);
                } else {
                    echo "<td style='text-align:center'>" . $dataKardex[$i]['cantidad'] . "</td>";
                    echo "<td style='text-align:right'>" . $costoUnitario . "</td>";
                    echo "<td style='text-align:right'>" . $dataKardex[$i]['cantidad'] * $costoUnitario . "</td>";
//                    echo "<td style='text-align:center'>" . $dataKardex[$i]['ORDEN'] . "</td>";

                    $cantidadTotal += $dataKardex[$i]['cantidad'];
                    $costoTotal = $cantidadTotal * $costoUnitario;

                    $cantidadEntrada += $dataKardex[$i]['cantidad'];
                    $costoTotalEntrada += ($cantidadTotal * $dataKardex[$i]['cantidad']);
                }

                echo "<td style='text-align:center'></td>";
                echo "<td style='text-align:right'></td>";
                echo "<td style='text-align:right'></td>";

                echo "<td style='text-align:center'>" . $cantidadTotal . "</td>";
                echo "<td style='text-align:right'>" . $costoUnitario . "</td>";
                echo "<td style='text-align:right'>" . $costoTotal . "</td>";
            } else {
                echo "<td style='text-align:center'></td>";
                echo "<td style='text-align:right'></td>";
                echo "<td style='text-align:right'></td>";

                echo "<td style='text-align:center'>" . $dataKardex[$i]['cantidad'] . "</td>";
                echo "<td style='text-align:right'>" . $costoUnitario . "</td>";
                echo "<td style='text-align:right'>" . $dataKardex[$i]['cantidad'] * $costoUnitario . "</td>";

                $cantidadTotal -= $dataKardex[$i]['cantidad'];
                $costoTotal = $cantidadTotal * $costoUnitario;

                $cantidadSalida += $dataKardex[$i]['cantidad'];
                $costoTotalSalida += ($dataKardex[$i]['cantidad'] * $costoUnitario);


                echo "<td style='text-align:center'>" . $cantidadTotal . "</td>";
                echo "<td style='text-align:right'>" . $costoUnitario . "</td>";
                echo "<td style='text-align:right'>" . $costoTotal . "</td>";
            }

            echo "<td style='text-align:center'>" . $dataKardex[$i]['ORDEN'] . "</td>";

            echo "</tr>";
        }
        echo "<tr>";
        echo "<td colspan=6></td>";
        echo "<th style='text-align:center'>" . $cantidadEntrada . "</td>";
        echo "<td></td>";
        echo "<th style='text-align:right'>" . $costoTotalEntrada . "</td>";
        echo "<th style='text-align:center'>" . $cantidadSalida . "</td>";
        echo "<td></td>";
        echo "<th style='text-align:right'>" . $costoTotalSalida . "</td>";
        echo "<td colspan=3></td>";
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

}

?>