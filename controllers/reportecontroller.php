<?php

class ReporteController extends ApplicationGeneral {

    //Lista de precios
    function ListaPrecios() {

        if (count($_REQUEST) == 6) {
            $linea = new Linea();
            $almacen = new Almacen();
            $opciones = new general();
            $url = "/" . $_REQUEST['url'];
            $data['Opcion'] = $opciones->buscaOpcionexurl($url);
            $data['Modulo'] = $opciones->buscaModulosxurl($url);
            $data['Linea'] = $linea->listadoLineas('idpadre=0');
            $data['Almacen'] = $almacen->listadoAlmacen();
            $this->view->show('reporte/listaprecio.phtml', $data);
        } else {
            $idAlmacen = $_REQUEST['idAlmacen'];
            $idLinea = $_REQUEST['idLinea'];
            $idSubLinea = $_REQUEST['idSubLinea'];
            $idProducto = $_REQUEST['idProducto'];
            $moneda = $_REQUEST['opcmoneda'];
            $reporte = new Reporte();
            $data = $reporte->reporteListaPrecio($idAlmacen, $idLinea, $idSubLinea, $idProducto);
            $rutaImagen = $this->rutaImagenesProducto();
            $unidadMedida = $this->unidadMedida();
            $data2 = array();
            for ($i = 0; $i < count($data); $i++) {
                //      echo '<td><img src="'.$rutaImagen.$data[$i]['codigo'].'/'.$data[$i]['imagen'].'" width="50" height="50"></td>';
                $data2[$i]['codigo'] = $data[$i]['codigopa'];
                $data2[$i]['nompro'] = $data[$i]['nompro'];
                $data2[$i]['stockactual'] = $data[$i]['stockactual'];
                if ($moneda == 1) {
                    $data2[$i]['preciolista'] = 'S/. ' . $data[$i]['preciolista'];
                }
                if ($moneda == 2) {
                    $data2[$i]['preciolista'] = 'U$. ' . $data[$i]['preciolistadolares'];
                }
                $data2[$i]['unidadmedida'] = $data[$i]['nombremedida'];
                $data2[$i]['empaque'] = empty($data[$i]['idempaque']) ? 'Sin/Emp.' : $data[$i]['codempaque'];
            }
            $objeto = $this->formatearparakui($data2);
            header("Content-type: application/json");
            //echo "{\"data\":" .json_encode($objeto). "}";
            echo json_encode($objeto);
        }
    }

    function inventario() {
        if (count($_REQUEST) == 6) {
            $linea = new Linea();
            $almacen = new Almacen();
            $url = "/" . $_REQUEST['url'];
            $opciones = new general();
            $data['Opcion'] = $opciones->buscaOpcionexurl($url);
            $data['Modulo'] = $opciones->buscaModulosxurl($url);
            $data['Linea'] = $linea->listadoLineas('idpadre=0');
            $data['Almacen'] = $almacen->listadoAlmacen();
            $this->view->show('/reporte/inventario.phtml', $data);
        } else {
            $idAlmacen = $_REQUEST['idAlmacen'];
            $idLinea = $_REQUEST['idLinea'];
            $idSubLinea = $_REQUEST['idSubLinea'];
            $idProducto = $_REQUEST['idProducto'];
            $producto = new Producto();
            $ordenCompra = new Ordencompra();
            $ordenVenta = new OrdenVenta();
            $dataProducto = $producto->inventario($idAlmacen, $idLinea, $idSubLinea, $idProducto);
            $dataOrdenCompra = $ordenCompra->inventario($idAlmacen, $idLinea, $idSubLinea, $idProducto);
            $dataOrdenVenta = $ordenVenta->inventario($idAlmacen, $idLinea, $idSubLinea, $idProducto);
            $rutaImagen = $this->rutaImagenesProducto();
            $unidadMedida = $this->unidadMedida();
            $empaque = $this->empaque();
            $data = array();
            $indice = 0;
            foreach ($dataProducto as $dato) {
                if (count($dataOrdenCompra)) {
                    foreach ($dataOrdenCompra as $doc) {
                        if ($doc['idproducto'] == $dato['idproducto']) {
                            $dato['stockporllegar'] = $doc['cantidadsolicitadaoc'];
                            break;
                        }
                    }
                }
                if (count($dataOrdenVenta)) {
                    foreach ($dataOrdenVenta as $dop) {
                        if ($dop['idproducto'] == $dato['idproducto']) {
                            $dato['stockpordespachar'] = $dop['cantaprobada'];
                            break;
                        }
                    }
                }
                //      echo '<td><img src="'.$rutaImagen.$dato['codigo'].'/'.$dato['imagen'].'" width="50" height="50"></td>';
                $data[$indice]['codigo'] = $dato['codigopa'];
                $data[$indice]['nompro'] = $dato['nompro'];
                $data[$indice]['preciolista'] = $dato['preciolista'];
                $data[$indice]['stockactual'] = $dato['stockactual'];
                $data[$indice]['stockporllegar'] = $dato['stockporllegar'];
                $data[$indice]['stockpordespachar'] = $dato['stockpordespachar'];
                $data[$indice]['unidadmedida'] = $dato['unidadmedida'];
                $data[$indice]['empaque'] = $empaque[($dato['empaque'])];
                $indice++;
            }
            $objeto = $this->formatearparakui($data);
            header("Content-type: application/json");
            //echo "{\"data\":" .json_encode($objeto). "}";
            echo json_encode($objeto);
        }
    }

    function estructuradecostos(){
        $this->view->show('/reporte/reporteestructuradecostos.phtml', $data);
    }

    function reporteCuadroAvanceMensual(){
        $this->view->show('/reporte/reporteCuadroAvanceMensual.phtml', $data);
    }

    //Kardex
    function Kardex() {
        if (count($_REQUEST) == 6) {
            $linea = new Linea();
            $almacen = new Almacen();
            $url = "/" . $_REQUEST['url'];
            $opciones = new general();
            $data['Modulo'] = $opciones->buscaModulosxurl($url);
            $data['Opcion'] = $opciones->buscaOpcionexurl($url);
            $data['Linea'] = $linea->listadoLineas('idpadre=0');
            $data['Almacen'] = $almacen->listadoAlmacen();
            $this->view->show('/reporte/kardex.phtml', $data);
        } else {
            $idAlmacen = $_REQUEST['idAlmacen'];
            $idLinea = $_REQUEST['idLinea'];
            $idSubLinea = $_REQUEST['idSubLinea'];
            $idProducto = $_REQUEST['idProducto'];
            $reporteKardex = new Reporte();
            $cliente = new Cliente();
            $orden = new Orden();
            $data = $reporteKardex->reporteKardex($idAlmacen, $idLinea, $idSubLinea, $idProducto);
            $unidadMedida = $this->unidadMedida();
            $tipoMovimiento = $this->tipoMovimiento();
            $data2 = array();
            for ($i = 0; $i < count($data); $i++) {
                $conceptoMovimiento = $this->conceptoMovimiento($data[$i]['tipomovimiento']);
                $nombreCliente = "";
                if ($data[$i]['idorden'] != null) {
                    $do = $orden->buscarxid($data[$i]['idorden']);
                    if ($do[0]['idcliente']) {
                        $dc = $cliente->buscaCliente($do[0]['idcliente']);
                        $nombreCliente = ($dc[0]['razonsocial'] != "") ? $dc[0]['razonsocial'] : $dc[0]['nombres'] . " " . $dc[0]['apellidopaterno'] . " " . $dc[0]['apellidomaterno'];
                    }
                }
                $data2[$i]['ndocumento'] = $data[$i]['ndocumento'];
                $data2[$i]['fechamovimiento'] = date('d/m/Y', strtotime($data[$i]['fechamovimiento']));
                $data2[$i]['conceptomovimiento'] = $conceptoMovimiento[($data[$i]['conceptomovimiento'])];
                $data2[$i]['tipomovimiento'] = substr($tipoMovimiento[($data[$i]['tipomovimiento'])], 0, 1);
                $data2[$i]['cantidad'] = $data[$i]['cantidad'];
                $data2[$i]['nombrecliente'] = $nombreCliente;
                $data2[$i]['stockdisponible'] = $data[$i]['stockdisponibledm'];
                $data2[$i]['unidadmedida'] = $unidadMedida[($data[$i]['unidadmedida'])];
                $data2[$i]['pu'] = number_format($data[$i]['pu'], 2);
                $data2[$i]['estadopedido'] = $data[$i]['estadopedido'];
            }
            $objeto = $this->formatearparakui($data2);
            header("Content-type: application/json");
            //echo "{\"data\":" .json_encode($objeto). "}";
            echo json_encode($objeto);
        }
    }

//Agotados
    function agotados() {
        if (count($_REQUEST) == 6) {
            $linea = new Linea();
            $almacen = new Almacen();
            $data['Linea'] = $linea->listadoLineas('idpadre=0');
            $data['Almacen'] = $almacen->listadoAlmacen();
            $this->view->show('/reporte/agotados.phtml', $data);
        } else {

            if (!empty($_REQUEST['fecha'])) {
                $fecha = date('d-m-Y', strtotime($_REQUEST['fecha']));
            }
            if (!empty($_REQUEST['fechaInicio'])) {
                $fechaInicio = date('d-m-Y', strtotime($_REQUEST['fechaInicio']));
            }
            if (!empty($_REQUEST['fechaFinal'])) {
                $fechaFinal = date('d-m-Y', strtotime($_REQUEST['fechaFinal']));
            }

            $idProducto = $_REQUEST['idProducto'];
            $repote = new Reporte();
            $ordenCompra = new Ordencompra();
            $linea = new Linea();
            //$rutaImagen=$this->rutaImagenesProducto();
            $data = $repote->reporteAgotados($fecha, $fechaInicio, $fechaFinal, $idProducto);
            //$data=$repote->reporteAgotados('','','','');
            $unidadMedida = $this->unidadMedida();
            for ($i = 0; $i < count($data); $i++) {
                $fu = ''; //Fecha ultima compra
                $fp = ''; //Fecha penultima compra
                $c1 = 0; //Cantidad 1
                $c2 = 0; //Cantidad 2
                $doc = $ordenCompra->lista2UltimasCompras($data[$i]['idproducto']);
                //Data orden compra
                $cantidadDoc = count($doc);
                if ($cantidadDoc) {
                    if ($cantidadDoc == 2) {
                        $fu = $doc[0]['fordencompra'];
                        $fp = $doc[1]['fordencompra'];
                        $c1 = $doc[0]['cantidadsolicitadaoc'];
                        $c2 = $doc[1]['cantidadsolicitadaoc'];
                    } else {
                        $fu = $doc[0]['fordencompra'];
                        $c1 = $doc[0]['cantidadsolicitadaoc'];
                    }
                }
                //><img src="'.$rutaImagen.$data[$i]['codigo'].'/'.$data[$i]['imagen'].'"></td>';
                $arreglo[$i]['codigo'] = $data[$i]['codigop'];
                $arreglo[$i]['nompro'] = $data[$i]['nompro'];
                $arreglo[$i]['fechaultima'] = date("d/m/Y", strtotime($fu));
                $arreglo[$i]['cantidadultima'] = $c1;
                $arreglo[$i]['fechapenultima'] = date("d/m/Y", strtotime($fp));
                $arreglo[$i]['cantidadpenultima'] = $c2;
                $arreglo[$i]['nomlin'] = $linea->nombrexid($data[$i]['idlinea']);
            }
            $dataAgotados = $this->formatearparakui($arreglo);
            header("Content-type: application/json");
            echo json_encode($dataAgotados);
        }
    }

    function contabilizables() {
        $linea = new Linea();
        $data['Linea'] = $linea->listadoLineas('idpadre=0');
        $this->view->show('/reporte/contabilizables.phtml', $data);
    }

    //Stock de producto
    function StockProducto() {
        if (count($_REQUEST) == 6) {
            $linea = new Linea();
            $almacen = new Almacen();
            $data['Linea'] = $linea->listadoLineas('idpadre=0');
            $data['Almacen'] = $almacen->listadoAlmacen();
            $this->view->show('/reporte/stockproducto.phtml', $data);
        } else {
            $idAlmacen = $_REQUEST['idAlmacen'];
            $idLinea = $_REQUEST['idLinea'];
            $idSubLinea = $_REQUEST['idSubLinea'];
            $idProducto = $_REQUEST['idProducto'];
            $repote = new Reporte();
            $data = $repote->reporteStockProducto($idAlmacen, $idLinea, $idSubLinea, $idProducto);
            $unidadMedida = $this->unidadMedida();
            $totalStock = 0;
            $data2 = array();
            $i = 0;
            for ($i = 0; $i < count($data); $i++) {
                $data2[$i]['codigo'] = $data[$i]['codigopa'];
                $data2[$i]['nompro'] = $data[$i]['nompro'];
                $data2[$i]['nomalm'] = $data[$i]['nomalm'];
                $data2[$i]['nomlin'] = $data[$i]['nomlin'];
                $data2[$i]['preciolista'] = $data[$i]['preciolista'];
                $data2[$i]['preciolistadolares'] = $data[$i]['preciolistadolares'];
                $data2[$i]['unidadmedida'] = $data[$i]['unidadmedida'];
                $data2[$i]['stockactual'] = $data[$i]['stockactual'];
                $data2[$i]['stockdisponible'] = ($data[$i]['stockdisponible']);
                $totalStock+=$data[$i]['stockactual'];
            }
            $objeto = $this->formatearparakui($data2);
            header("Content-type: application/json");
            echo json_encode($objeto);
        }
    }

    //Stock de producto Repuesto
    function StockProductoRepuesto() {
        if (count($_REQUEST) == 6) {
            $linea = new Linea();
            $almacen = new Almacen();
            $data['Linea'] = $linea->listadoLineas('idpadre=0');
            $data['Almacen'] = $almacen->listadoAlmacen();
            $this->view->show('/reporte/stockproductorepuesto.phtml', $data);
        } else {
            $idAlmacen = $_REQUEST['idAlmacen'];
            $idLinea = $_REQUEST['idLinea'];
            $idSubLinea = $_REQUEST['idSubLinea'];
            $idProducto = $_REQUEST['idProducto'];
            $repote = new Reporte();
            $data = $repote->reporteStockProductoRep($idAlmacen, $idLinea, $idSubLinea, $idProducto);
            $unidadMedida = $this->unidadMedida();
            $totalStock = 0;
            $data2 = array();
            $i = 0;
            for ($i = 0; $i < count($data); $i++) {
                $data2[$i]['codigo'] = $data[$i]['codigopa'];
                $data2[$i]['nompro'] = $data[$i]['nompro'];
                $data2[$i]['nomalm'] = $data[$i]['nomalm'];
                $data2[$i]['nomlin'] = $data[$i]['nomlin'];
                $data2[$i]['preciolista'] = $data[$i]['preciolista'];
                $data2[$i]['preciolistadolares'] = $data[$i]['preciolistadolares'];
                $data2[$i]['unidadmedida'] = $data[$i]['unidadmedida'];
                $data2[$i]['stockactual'] = $data[$i]['stockactual'];
                $data2[$i]['stockdisponible'] = ($data[$i]['stockdisponible']);
                $totalStock+=$data[$i]['stockactual'];
            }
            $objeto = $this->formatearparakui($data2);
            header("Content-type: application/json");
            echo json_encode($objeto);
        }
    }

//Reporte de orden de compra
    function ordenCompra() {
        if (count($_REQUEST) == 6) {
            $ordenCompra = new Ordencompra();
            /* $url="/".$_REQUEST['url'];
              $opciones=new general();
              $data['Opcion']=$opciones->buscaOpcionexurl($url);
              $data['Modulo']=$opciones->buscaModulosxurl($url); */
            $data['Ordencompra'] = $ordenCompra->listadoOrdenescompra();
            $this->view->show("/reporte/ordencompra.phtml", $data);
        } else {
            $idProveedor = $_REQUEST['idProveedor'];
            $fecha = $_REQUEST['fecha'];
            $fechaInicio = $_REQUEST['fechaInicio'];
            $fechaFinal = $_REQUEST['fechaFinal'];
            $repote = new Reporte();
            $data = $repote->reporteOrdenCompra($idProveedor, $fecha, $fechaInicio, $fechaFinal);
            $data2 = array();
            for ($i = 0; $i < count($data); $i++) {
                $data2[$i]['codigooc'] = $data[$i]['codigooc'];
                $data2[$i]['fordencompra'] = date("d/m/Y", strtotime($data[$i]['fordencompra']));
                $data2[$i]['nomalm'] = $data[$i]['nomalm'] . '</td>';
                $data2[$i]['razonsocialp'] = $data[$i]['razonsocialp'];
                $data2[$i]['fob'] = $data[$i]['fob'];
            }
            $objeto = $this->formatearparakui($data2);
            header("Content-type: application/json");
            echo json_encode($objeto);
        }
    }

    //Reporte de stock valorizado
    function reporteStockValorizado() {
        if (count($_REQUEST) == 6) {
            $linea = new Linea();
            $data['Linea'] = $linea->listadoLineas("idpadre=0");
            $this->view->show("/reporte/stockvalorizado.phtml", $data);
        } else {
            $idLinea = $_REQUEST['linea'];
            $idSubLinea = $_REQUEST['sublinea'];
            $reporte = new Reporte();
            $data = $reporte->reporteStockValorizado($idLinea, $idSubLinea);
            $total = 0;
            $data2 = array();
            for ($i = 0; $i < count($data); $i++) {
                $data2[$i]['codigo'] = $data[$i]['codigop'];
                $data2[$i]['nompro'] = $data[$i]['nompro'];
                $data2[$i]['nomalm'] = $data[$i]['nomalm'];
                $data2[$i]['nomlin'] = $data[$i]['nomlin'];
                $data2[$i]['unidadmedida'] = $data[$i]['unidadmedida'];
                $data2[$i]['stock'] = $data[$i]['stockactual'];
                $data2[$i]['precio'] = number_format($data[$i]['preciolista'], 2);
                $data2[$i]['preciototal'] = number_format(($data[$i]['stockactual'] * $data[$i]['preciolista']), 2);
                $total+=($data[$i]['stockactual'] * $data[$i]['preciolista']);
            }
            $objeto = $this->formatearparakui($data2);
            header("Content-type: application/json");
            echo json_encode($objeto);
            //echo '<tr style="font-weight:bold"><td colspan="6"></td><td class="right">Total:</td><td class="right">'.number_format($total,2).'</td></tr>';
        }
    }

    //Actualiza los datos del stock disponible y actual
    function update() {

        if (count($_REQUEST) == 6) {
            $linea = new Linea();
            $almacen = new Almacen();
            $data['Linea'] = $linea->listadoLineas('idpadre=0');
            $data['Almacen'] = $almacen->listadoAlmacen();
            $this->view->show('/reporte/update.phtml', $data);
        }
    }

    function updateStock() {
        //$Stockdisponible=$_REQUEST['stockDisponible'];
        $conteo1 = $_REQUEST['stockActual'];
        $idproducto = $_REQUEST['idproducto'];

        $repote = new Reporte();
        $data = $repote->UpdateStockProducto($conteo1, $idproducto);
        if ($data) {
            echo"campo actualizado";
        }
    }

    function precioTotalStockValorizado() {
        $reporte = new Reporte();
        $idLinea = $_REQUEST['linea'];
        $idSubLinea = $_REQUEST['sublinea'];
        $suma = $reporte->totalesStockValorizado($idLinea, $idSubLinea);
    }

    //Reporte de ventas
    function ventas() {
        $linea = new Linea();
        $vendedor = new Actor();
        if (count($_REQUEST) == 6) {
            $tamanio = 10;
            $ordenVenta = new OrdenVenta();
            $linea = new Linea();
            $vendedor = new Actor();
            $data['linea'] = $linea->listadoLineas('idpadre=0');
            $data['vendedor'] = $vendedor->listadoVendedoresTodos();
            $data['Paginacion'] = $ordenVenta->Paginacion($tamanio);
            $data['Pagina'] = 1;
            $this->view->show('/reporte/ventas.phtml', $data);
        } else {
            $idLinea = $_REQUEST['linea'];
            $idVendedor = $_REQUEST['vendedor'];
            $fInicial = $_REQUEST['fechaInicial'];
            $fFinal = $_REQUEST['fechaFinal'];
            $ordenVenta = new OrdenVenta();
            $data = $ordenVenta->listadoReporteVentas($idLinea, $idVendedor, $fInicial, $fFinal);
            //$data = $ordenVenta->listadoReporteVentas($idLinea, $idVendedor, '2012/09/07', '2012/09/07');
            //$objeto = $this->formatearparakui($data);
            //header("Content-type: application/json");
            echo json_encode($data);
        }
    }

    function prueba() {
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->listadoReporteVentas("6", "", "2012/08/01", "2012/09/05");
        $objeto = $this->formatearparakui($data);
        echo json_encode($objeto);
    }

    function letras() {
        if (count($_REQUEST) == 6) {
            $this->view->show("/reporte/letras.phtml");
        } else {
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
                $indice+=1;
                $dcuenta = $ordenventa->cuentasxidordenventa($dordenventa[$i]['idordenventa']);
                $total2 = count($dcuenta);
                for ($j = 0; $j < $total2; $j++) {
                    if ($dcuenta[$j]['formacobro'] == 3) {
                        $cuenta[$indice]['codigov'] = $dordenventa[$i]['codigov'];
                        $cuenta[$indice]['importeov'] = "";
                        $cuenta[$indice]['idcondicionletra'] = "";
                        $cuenta[$indice]['situacion'] = "";
                        $cuenta[$indice]['fvencimiento'] = "";
                        $cuenta[$indice]['saldo'] = "";
                        $cuenta[$indice]['importedoc'] = number_format($dcuenta[$j]['importedoc'], 4);
                        $cuenta[$indice]['formacobro'] = (($dcuenta[$j]['formacobro'] == 1) ? 'CONTADO' : (($dcuenta[$j]['formacobro'] == 4) ? 'LETRA' : 'CREDITO'));
                        $cuenta[$indice]['situacionc'] = (($dcuenta[$j]['situacion'] == 0) ? 'PENDIENTE' : (($dcuenta[$j]['situacion'] == 1) ? 'CANCELADO' : 'DESDOBLADO'));
                        $cuenta[$indice]['fvencimientoc'] = $dcuenta[$j]['fvencimiento'];
                        $indice+=1;
                    }
                }
            }
            $objeto = $this->formatearparakui($cuenta);
            header("Content-type: application/json");
            //echo "{\"data\":" .json_encode($objeto). "}";
            echo json_encode($objeto);
        }
    }

    function reportletras() {
        $zona = $this->AutoLoadModel('zona');
        $actor = $this->AutoLoadModel('actorrol');
        $tipoCobranza = $this->AutoLoadModel('tipocobranza');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['hijo'] = $zona->listacategoriaHijo();
        $data['zona'] = $zona->listadoTotalZona();
        $data['tipocobranza'] = $tipoCobranza->listaNueva();
        $data['vendedor'] = $actor->actoresxRolxNombreSinconEstado(25);
        $data['cobrador'] = $actor->actoresxRolxNombre(28);
        
        $Numerounicomodel=$this->AutoLoadModel("Numerounico");
        $data['numerosunicos'] = $Numerounicomodel->listarNumerounico();
        $this->view->show('/reporte/reportletras.phtml', $data);
    }

  
      function reporteletras() {
        set_time_limit(1000);
        // Crearción de instacias de reporte, tipocobranza, ordengasto, tipocobro y movimiento:
        $reporte = $this->AutoLoadModel('reporte');
        $tipo = $this->AutoLoadModel('tipocobranza');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $tipoCobroIni = $this->configIniTodo('TipoCobro');
        $movimiento = $this->AutoLoadModel('movimiento');
        // recepción de variables:
        $idzona = $_REQUEST['idzona'];
        $idcategoriaprincipal = $_REQUEST['idcategoriaprincipal'];
        $idcategoria = $_REQUEST['idcategoria'];
        $idvendedor = $_REQUEST['idvendedor'];
        $idtipocobranza = $_REQUEST['idtipocobranza'];
        $idtipocobro = $_REQUEST['idtipocobro'];
        $fechaInicio = $_REQUEST['fechaInicio'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        $pendiente = $_REQUEST['pendiente'];
        $cancelado = $_REQUEST['cancelado'];
        $octava = $_REQUEST['octava'];
        $novena = $_REQUEST['novena'];
        $idcobrador = $_REQUEST['idcobrador'];
        $IdCliente = $_REQUEST['IdCliente'];
        $IdOrdenVenta = $_REQUEST['IdOrdenVenta'];
        $recepcionLetras = $_REQUEST['recepcionLetras'];
        $orderDireccion = $_REQUEST['orderDireccion'];


        $octavaNovena = " ";
        if (!empty($octava) && !empty($novena)) {
            $octavaNovena.=" and (wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 8 DAY) or wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 9 DAY)) and wc_detalleordencobro.`situacion`='' ";
        } elseif (!empty($novena)) {

            $octavaNovena.=" and wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 9 DAY) and wc_detalleordencobro.`situacion`='' ";
        } elseif (!empty($octava)) {
            $octavaNovena.=" and wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 8 DAY) and wc_detalleordencobro.`situacion`='' ";
        }

        $situacion = "";
        if (!empty($pendiente) && !empty($cancelado)) {
            $situacion.=" and (wc_detalleordencobro.`situacion`='' or wc_detalleordencobro.`situacion`='cancelado') ";
        } elseif (!empty($cancelado)) {
            $situacion.=" and wc_detalleordencobro.`situacion`='cancelado' ";
        } elseif (!empty($pendiente)) {
            $situacion.=" and wc_detalleordencobro.`situacion`='' ";
        }
        if ($_REQUEST['fechaInicio'] != "") {
            $fechaInicio = date('Y-m-d', strtotime($_REQUEST['fechaInicio']));
        }
        $fechaFinal = $_REQUEST['fechaFinal'];
        if ($_REQUEST['fechaFinal'] != "") {
            $fechaFinal = date('Y-m-d', strtotime($_REQUEST['fechaFinal']));
        }
        if ($_REQUEST['fechaPagoInicio'] != "") {
            $fechaPagoInicio = date('Y-m-d', strtotime($_REQUEST['fechaPagoInicio']));
        } else {
            $fechaPagoInicio = $_REQUEST['fechaPagoInicio'];
        }
        if ($_REQUEST['fechaPagoFinal'] != "") {
            $fechaPagoFinal = date('Y-m-d', strtotime($_REQUEST['fechaPagoFinal']));
        } else {
            $fechaPagoFinal = $_REQUEST['fechaPagoFinal'];
        }
        $idcategorias = "";
        if (!empty($idcobrador)) {
            $cobrador = $this->AutoLoadModel('cobrador');
            $dataCobrador = $cobrador->buscaZonasxCobrador($idcobrador);
            $cantidadCobrador = count($dataCobrador);
            if ($cantidadCobrador != 0) {
                $idcategorias.=" and (";
                for ($i = 0; $i < $cantidadCobrador; $i++) {
                    if ($i == 0) {
                        $idcategorias.=" wc_categoria.`idcategoria`='" . $dataCobrador[$i]['idzona'] . "' ";
                    } else {
                        $idcategorias.=" or wc_categoria.`idcategoria`='" . $dataCobrador[$i]['idzona'] . "' ";
                    }
                }
                $idcategorias.=" ) ";
            } else {
                $idcategorias.=" and  wc_categoria.`idcategoria`='0' ";
            }
        } elseif (!empty($idcategoria)) {
            $idcategorias = " and wc_categoria.`idcategoria`='" . $idcategoria . "' ";
        }
        if ($idtipocobro == 3) {//letras al banco
            $filtro = "wc_detalleordencobro.`formacobro`='3' and wc_ordencobro.`tipoletra`=1";
//            if(!empty($recepcionLetras)){
//                if($recepcionLetras == 1){
//                    $filtro.="and wc_detalleordencobro.`recepcionLetras`='PA'";
//                }else{
//                    $filtro.="and wc_detalleordencobro.`recepcionLetras`=''";
//                }
//            }
        } elseif ($idtipocobro == 4) {//letras cartera
            $filtro = "wc_detalleordencobro.`formacobro`='3' and  wc_ordencobro.`tipoletra`=2";
        } elseif ($idtipocobro == 2) {//credito
            $filtro = "wc_detalleordencobro.`formacobro`='2' and wc_detalleordencobro.referencia=''";
        } elseif ($idtipocobro == 1) {//al contado
            $filtro = "wc_detalleordencobro.`formacobro`='1' ";
        } elseif ($idtipocobro == 5) {//letras protestadas
            $filtro = "wc_detalleordencobro.`formacobro`='2' and (substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p')";
            $dias = 10;
        }

        $totalPagado = 0;
        $totalImporte = 0;
        $importe = 0;
        $totalDevolucion = 0;
        $total = 0;
        $TPagado = 0;
        $cont = 0;
        $fechaActual = date('Y-m-d');
        $datareporte = $reporte->reportletras($filtro, $idzona, $idcategoriaprincipal, $idcategorias, $idvendedor, $idtipocobranza, $fechaInicio, $fechaFinal, $octavaNovena, $situacion, $fechaPagoInicio, $fechaPagoFinal, $IdCliente, $IdOrdenVenta,$orderDireccion);
//
        $dataAnterior = $datareporte[-1]['idordenventa'];


        echo "<thead>
                    <tr>
                            <th >Codigo</th>
                            <th class='ocultarImpresion'>Vendedor</th>
                            <th class='mostrarImpresion' style='display:none'>Ven</th>
                            <th class='ocultarImpresion'>Zona Cobranza</th>
                            <th class='ocultarImpresion'>Zona </th>
                            <th>F. Des.".$orderDireccion."</th>
                            <th>F. venc.</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Pagado</th>
                            <th>Devol.</th>
                            <th>Deuda</th>
                            <th class='ocultarImpresion'>Tipo Cobranza</th>
                            <th>" . date('d/m') . "</th>
                            <th>" . date('d/m', strtotime("$fechaActual + 1 day")) . "</th>
                            <th>" . date('d/m', strtotime("$fechaActual + 2 day")) . "</th>
                            <th>" . date('d/m', strtotime("$fechaActual + 3 day")) . "</th>
                            <th>" . date('d/m', strtotime("$fechaActual + 4 day")) . "</th>

                    </tr>
                    <tr class='ocultarImpresion'><td colspan='10'>&nbsp;</td></tr>
              </thead>
              <tbody>";

        $cantidadreporte = count($datareporte);

//        echo "<tr><td>";

//        echo "--------------------------------------------------------".$recepcionLetras."<br>";
        //var_dump($datareporte);

//        echo "--------------------------------------------------------<br>";

        if ($idtipocobro == 3 && !empty($recepcionLetras)){

            $auxDatareporte = array();

//            echo "recepion::".$recepcionLetras."<br>";

            if($recepcionLetras == 1){
                $comp = 'PA';
            }else if($recepcionLetras == 2){
                $comp = '';
            }
            $auxcont = 0;
            for($i = 0; $i< $cantidadreporte;$i++){
                if ($dataAnterior != $datareporte[$i]['idordenventa']) {
                    $dataAnterior = $datareporte[$i]['idordenventa'];
                }
//                echo $datareporte[$i]['recepcionletras']."==".$comp."??<br>";
                if($datareporte[$i]['recepcionletras'] == $comp){
                    $auxDatareporte[$auxcont] = $datareporte[$i];
                    $auxcont++;
                }
            }
            $datareporte = $auxDatareporte;
        }

//        echo "--------------------------------------------------------: ".$auxcont."<br>";
//        var_dump($auxDatareporte);
//        echo "--------------------------------------------------------<br>";
//        var_dump($datareporte);
//        echo "--------------------------------------------------------<br>";
//        echo "</td></tr>";

        for ($i = 0; $i < $cantidadreporte; $i++) {
            if (!empty($dias)) {
                $datareporte[$i]['diffechas'] = $datareporte[$i]['diffechas'] + 10;
            }
            $simbolomoneda = $datareporte[$i]['simbolo'];
            if (strcasecmp($datareporte[$i]['situacion'], '') == 0) {
                $color = "style='color:red;text-align:right;'";
                $total+=$datareporte[$i]['saldodoc'];
            } else {
                $color = "style='color:blue;text-align:right;'";
                $totalPagado+=$datareporte[$i]['importedoc'] - $datareporte[$i]['saldodoc'];
            }
            if ($dataAnterior != $datareporte[$i]['idordenventa']) {

                $dataAnterior = $datareporte[$i]['idordenventa'];
                $dataTipoCobranza = $tipo->buscaxid($datareporte[$i]['idtipocobranza']);
                $tipocobranza = !empty($dataTipoCobranza[0]['nombre']) ? $dataTipoCobranza[0]['nombre'] : 'Sin Asignar';
                $importe = $ordenGasto->totalGuia($datareporte[$i]['idordenventa']);
                $percepcion = $ordenGasto->ImporteGastoxIdDetalleOrdenCobro($datareporte[$i]['iddetalleordencobro']);
                $acumulaxIdMoneda[$simbolomoneda]['totalImporte']+=$importe;
                $acumulaxIdMoneda[$simbolomoneda]['TPagado']+=$datareporte[$i]['importepagado'];
                $acumulaxIdMoneda[$simbolomoneda]['totalDevolucion']+=$datareporte[$i]['importedevolucion'];
                $acumulaxIdMoneda[$simbolomoneda]['totalDeuda'] = $acumulaxIdMoneda[$simbolomoneda]['totalImporte'] - $acumulaxIdMoneda[$simbolomoneda]['TPagado'];

                echo "<tr style='border-radius:10px;background-color:rgb(124, 180, 224)'>
                                                 <td style='width:18mm'>" . $datareporte[$i]['codigov'] . "</td>
                                                 <td class='ocultarImpresion'>" . substr($datareporte[$i]['codigoa'] . ' ' . $datareporte[$i]['apellidopaterno'] . ' ' . $datareporte[$i]['apellidomaterno'] . ' ' . $datareporte[$i]['nombres'], 0, 20) . "</td>
                                                 <td class='mostrarImpresion' style='display:none'>" . $datareporte[$i]['codigoa'] . "</td>
                                                 <td class='ocultarImpresion'>" . $datareporte[$i]['nombrec'] . "</td>
                                                 <td class='ocultarImpresion'>" . $datareporte[$i]['nombrezona'] . "</td>
                                                 <td>" . date('d/m/y', strtotime($datareporte[$i]['fechadespacho'])) . "</td>
                                                 <td>" . date('d/m/y', strtotime($datareporte[$i]['fechavencimiento'])) . "</td>
                                                 <td style='width:36mm'>" . $datareporte[$i]['razonsocial'] . "</td>
                                                 <td>" . $simbolomoneda . " " . number_format($importe, 2) . "</td>
                                                 <td>" . $simbolomoneda . " " . number_format($datareporte[$i]['importepagado'], 2) . "</td>
                                                 <td>" . $simbolomoneda . " " . number_format($datareporte[$i]['importedevolucion'], 2) . "</td>
                                                 <td>" . $simbolomoneda . " " . number_format($importe - $datareporte[$i]['importepagado'] - $datareporte[$i]['importedevolucion'], 2) . "</td>
                                                 <td class='ocultarImpresion'>" . $tipocobranza . "</td>
                                                 <td style='width:15mm;border:1px solid;'>&nbsp;</td>
                                                <td style='width:15mm;border:1px solid;'>&nbsp;</td>
                                                <td style='width:15mm;border:1px solid;'>&nbsp;</td>
                                                <td style='width:15mm;border:1px solid;'>&nbsp;</td>
                                                <td style='width:15mm;border:1px solid;'>&nbsp;</td>
                                        </tr>";
                echo "<tr  class='filaContenedor' style='padding-left:0px ;border:solid 1px;'>
                                                <td colspan='18'>
                                                        <table class='filaOculta' style='display:none;margin:0px'><tr><td colspan='15'><a class='ver' href='#'>&nbsp<img src='/imagenes/iconos/OrdenAbajo.gif'></a></td></tr></table>
                                                        <table class='tblchildren' style='margin:0px;padding:0px;'>
                                                                <thead>
                                                                        <tr class=''>
                                                                                <th style='width:70mm'>Direccion</th>

                                                                                <th style='width:30mm'>Estado</th>
                                                                                <th style='width:15mm'>Cond. Venta</th>
                                                                                <th style='width:10mm'>N° Letra</th>
                                                                                <th style='width:15mm'>F. Girooo</th>
                                                                                <th style='width:15mm'>F. Ven.</th>
                                                                                <th style='width:15mm'>F. Can.</th>
                                                                                <th>N° Unico</th>
                                                                                <th>Indicador</th>
                                                                                <th>Importe</th>
                                                                                <th>Percepcion</th>
                                                                                <th>Protesto</th>
                                                                                <th>Total</th>
                                                                                <th>Saldo</th>
                                                                                <th>Situacion</th>
                                                                                <th style='width:25mm'>Referencia <a class='ocultar' style='margin-left:0px;' href='#'><img src='/imagenes/iconos/OrdenArriba.gif'></a></th>
                                                                        </tr>
                                                                </thead>
                                                                <tbody>";
            }
            echo "<tr style='margin-botton:none;'>";
            if ($cont == 0) {
                echo "<td >" . $datareporte[$i]['direccion']."</td>";


                $cont++;
            }else {
                echo "<td >&nbsp;</td>";
            }
            
            $temporalImporteDoc = $datareporte[$i]['importedoc'];
            $temporalTOALImporteDoc = $datareporte[$i]['importedoc'];
            $temporalmontoprotesto = '';
            /*
            if ($temporalImporteDoc-$percepcion <=0 ) {
                $temporalImporteDoc = '-';
                $temporalTOALImporteDoc = $percepcion;
            }
            */
            $datareporte[$i]['proviene']  = strtoupper($datareporte[$i]['proviene']);
            if (($datareporte[$i]['proviene'] == 'GAST. PROTE.' || $datareporte[$i]['proviene'] == 'PROTE.')) {
                $temporalmontoprotesto = $datareporte[$i]['montoprotesto'];
                $temporalImporteDoc = $temporalImporteDoc - $temporalmontoprotesto;
                $temporalmontoprotesto = $simbolomoneda . " " . $temporalmontoprotesto;
            }

            echo "
                    <td ><h4><strong>" . ($dias == 10 ? 'PROTESTO - ' : "") . "</strong>" . ($datareporte[$i]['idtipocobranza'] == 4 ? 'INCOBRABLES' : strtoupper($tipo->NombreTipoCobranzaxDiasVencidos($datareporte[$i]['diffechas']))) . "</h4></td>
                    <td style='text-align:center'>" . $tipoCobroIni[$datareporte[$i]['formacobro']] . "</td>
                    <td >" . ($datareporte[$i]['numeroletra']) . "</td>
                    <td >" . date('d/m/y', strtotime($datareporte[$i]['fechagiro'])) . "</td>
                    <td >" . date('d/m/y', strtotime($datareporte[$i]['fvencimiento'])) . "</td>
                    <td >" . $this->FechaFormatoCorto($datareporte[$i]['fechapago']) . "</td>
                    <td >" . $datareporte[$i]['numerounico'] . "</td>
                    <td >" . $datareporte[$i]['recepcionletras'] . "</td>
                    <td >" . $temporalImporteDoc . "</td>
                    <td >" . (!empty($percepcion) ? ($simbolomoneda . " " . number_format($percepcion, 2)) : '') . "</td>
                    <td >" . $temporalmontoprotesto . "</td>
                    <td >" . $simbolomoneda . " " . $temporalTOALImporteDoc . "</td>    
                    <td >" . $simbolomoneda . " " . number_format($datareporte[$i]['saldodoc'], 2) . "</td>
                    <td >" . ($datareporte[$i]['situacion'] == '' ? 'Pendiente' : $datareporte[$i]['situacion']) . "</td>
                    <td >" . strtoupper($datareporte[$i]['proviene'] . " " . substr($datareporte[$i]['referencia'], 0, 11)) . "</td>
            </tr>"; // GAST. PROTE.
            if ($dataAnterior != $datareporte[$i + 1]['idordenventa']) {
                $cont = 0;
                echo "<tr> <th colspan='1'>Telefono / Celular: </th> <td colspan='7'>" . $datareporte[$i]['telefono']."</td> <th colspan='2'>Atiende: </th> <td colspan='6'>" . $datareporte[$i]['contacto']."</td> </tr>"."</tbody>
                                                        </table>
                                                </td>

                                        </tr>";
            }
        }
         //*/

        echo "</tbody>
                    <tfoot>
                           <tr><th colspan='2' style='text-align:right;'>Total</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalImporte'], 2) . "</td><th colspan='2' style='text-align:right;'>Total Pagado</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['TPagado'], 2) . "</td><th  style='text-align:right;' colspan='2'>Total Devolucion</th><td style='text-align:right;' colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalDevolucion'], 2) . "</td ><th colspan='2'>Total Deuda</th><td colspan='3'>S/. " . number_format($acumulaxIdMoneda['S/']['totalDeuda']-$acumulaxIdMoneda['S/']['totalDevolucion'], 2) . "</td></tr>
                           <tr><th colspan='2' style='text-align:right;'>Total</th><td colspan='2'>US $. " . number_format($acumulaxIdMoneda['US $']['totalImporte'], 2) . "</td><th colspan='2' style='text-align:right;'>Total Pagado</th><td colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['TPagado'], 2) . "</td><th  style='text-align:right;' colspan='2'>Total Devolucion</th><td style='text-align:right;' colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['totalDevolucion'], 2) . "</td ><th colspan='2'>Total Deuda</th><td colspan='3'>US $ " . number_format($acumulaxIdMoneda['US $']['totalDeuda']-$acumulaxIdMoneda['US $']['totalDevolucion'], 2) . "</td></tr>
                    </tfoot>
             ";
         
/*
              echo "</tbody>
                    <tfoot>
                           <tr>
                                <th colspan='1' style='text-align:right;font-size:11px !important;'>Total</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalImporte'], 2) . "</td>
                                <th colspan='1' style='text-align:right;font-size:11px !important;'>Total Pagado</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['TPagado'], 2) . "</td>
                                <th  style='text-align:right;font-size:11px !important;' colspan='1'>Total Devolucion</th><td style='text-align:right;' colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalDevolucion'], 2) . "</td >
                                <th colspan='2' style='text-align:right;font-size:11px !important;'>Total Deuda Sin Devoluciones</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalDeuda'], 2) . "</td>
                                <th colspan='2' style='text-align:right;font-size:11px !important;'>Total Deuda Con Devoluciones</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalDeuda']-$acumulaxIdMoneda['S/']['totalDevolucion'], 2) . "</td>
                            </tr>
                           <tr>
                                <th colspan='1' style='text-align:right;font-size:11px !important;'>Total</th><td colspan='2'>US $. " . number_format($acumulaxIdMoneda['US $']['totalImporte'], 2) . "</td>
                                <th colspan='1' style='text-align:right;font-size:11px !important;'>Total Pagado</th><td colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['TPagado'], 2) . "</td>
                                <th  style='text-align:right;font-size:11px !important;' colspan='1'>Total Devolucion</th><td style='text-align:right;' colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['totalDevolucion'], 2) . "</td >
                                <th colspan='2' style='text-align:right;font-size:11px !important;'>Total Deuda Sin Devoluciones</th><td colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['totalDeuda'], 2) . "</td>
                                <th colspan='2' style='text-align:right;font-size:11px !important;'>Total Deuda Con Devoluciones</th><td colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['totalDeuda']-$acumulaxIdMoneda['US $']['totalDevolucion'], 2) . "</td>
                           </tr>

                    </tfoot>";
  */      
    }


    function reporteletrasdetalladas() {
        set_time_limit(1000);
        $reporte = $this->AutoLoadModel('reporte');
        $tipo = $this->AutoLoadModel('tipocobranza');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $tipoCobroIni = $this->configIniTodo('TipoCobro');
        $movimiento = $this->AutoLoadModel('movimiento');
        $detalleordencobro = $this->AutoLoadModel('detalleordencobro');
        $detalleordencobroingreso = $this->AutoLoadModel('detalleordencobroingreso');
        $idzona = $_REQUEST['idzona'];
        $idcategoriaprincipal = $_REQUEST['idcategoriaprincipal'];
        $idcategoria = $_REQUEST['idcategoria'];
        $idvendedor = $_REQUEST['idvendedor'];
        $idtipocobranza = $_REQUEST['idtipocobranza'];
        $idtipocobro = $_REQUEST['idtipocobro'];
        $fechaInicio = $_REQUEST['fechaInicio'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        $pendiente = $_REQUEST['pendiente'];
        $cancelado = $_REQUEST['cancelado'];
        $octava = $_REQUEST['octava'];
        $novena = $_REQUEST['novena'];
        $idcobrador = $_REQUEST['idcobrador'];
        $IdCliente = $_REQUEST['IdCliente'];
        $IdOrdenVenta = $_REQUEST['IdOrdenVenta'];
        $vendedor = $_REQUEST['vendedor'];
        $tipocobro = $_REQUEST['tipocobro'];
        $tipoBanco = $_REQUEST['tipoBanc'];
        $recepLetras = $_REQUEST['recepcionLetras'];

        $octavaNovena = " ";
        if (!empty($octava) && !empty($novena)) {
            $octavaNovena .= " and (wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 8 DAY) or wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 9 DAY)) and wc_detalleordencobro.`situacion`='' ";
        } elseif (!empty($novena)) {

            $octavaNovena .= " and wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 9 DAY) and wc_detalleordencobro.`situacion`='' ";
        } elseif (!empty($octava)) {

            $octavaNovena .= " and wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 8 DAY) and wc_detalleordencobro.`situacion`='' ";
        }

        $situacion = "";
        if (!empty($pendiente) && !empty($cancelado)) {
            $situacion .= " and (wc_detalleordencobro.`situacion`='' or wc_detalleordencobro.`situacion`='cancelado') ";
        } elseif (!empty($cancelado)) {
            $situacion .= " and wc_detalleordencobro.`situacion`='cancelado' ";
        } elseif (!empty($pendiente)) {
            $situacion .= " and wc_detalleordencobro.`situacion`='' ";
        }
        if ($_REQUEST['fechaInicio'] != "") {
            $fechaInicio = date('Y-m-d', strtotime($_REQUEST['fechaInicio']));
        }
        $fechaFinal = $_REQUEST['fechaFinal'];
        if ($_REQUEST['fechaFinal'] != "") {
            $fechaFinal = date('Y-m-d', strtotime($_REQUEST['fechaFinal']));
        }
        if ($_REQUEST['fechaPagoInicio'] != "") {
            $fechaPagoInicio = date('Y-m-d', strtotime($_REQUEST['fechaPagoInicio']));
        } else {
            $fechaPagoInicio = $_REQUEST['fechaPagoInicio'];
        }
        if ($_REQUEST['fechaPagoFinal'] != "") {
            $fechaPagoFinal = date('Y-m-d', strtotime($_REQUEST['fechaPagoFinal']));
        } else {
            $fechaPagoFinal = $_REQUEST['fechaPagoFinal'];
        }
        $idcategorias = "";
        if (!empty($idcobrador)) {
            $cobrador = $this->AutoLoadModel('cobrador');
            $dataCobrador = $cobrador->buscaZonasxCobrador($idcobrador);
            $cantidadCobrador = count($dataCobrador);
            if ($cantidadCobrador != 0) {
                $idcategorias .= " and (";
                for ($i = 0; $i < $cantidadCobrador; $i++) {
                    if ($i == 0) {
                        $idcategorias .= " wc_categoria.`idcategoria`='" . $dataCobrador[$i]['idzona'] . "' ";
                    } else {
                        $idcategorias .= " or wc_categoria.`idcategoria`='" . $dataCobrador[$i]['idzona'] . "' ";
                    }
                }
                $idcategorias .= " ) ";
            } else {
                $idcategorias .= " and  wc_categoria.`idcategoria`='0' ";
            }
        } elseif (!empty($idcategoria)) {
            $idcategorias = " and wc_categoria.`idcategoria`='" . $idcategoria . "' ";
        }


        if ($idtipocobro == 3) {//letras al banco
            $filtro = "wc_detalleordencobro.`formacobro`='3' and wc_ordencobro.`tipoletra`=1";
        } elseif ($idtipocobro == 4) { //letras Cartera
            $filtro = "wc_detalleordencobro.`formacobro`='3' and  wc_ordencobro.`tipoletra`=2";
        } elseif ($idtipocobro == 2) {//credito
            $filtro = "wc_detalleordencobro.`formacobro`='2' and wc_detalleordencobro.referencia=''";
        } elseif ($idtipocobro == 1) {//al contado
            $filtro = "wc_detalleordencobro.`formacobro`='1' ";
        } elseif ($idtipocobro == 5) {//letras protestadas
            $filtro = "wc_detalleordencobro.`formacobro`='2' and (substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p')";
            $filtro .= "and wc_zona.`nombrezona` not like '%incobrab%'";
            $dias = 10;
        }

        $TOTALIMPORTE2 = 0;

        $totalPagado = 0;
        $totalImporte = 0;
        $importe = 0;
        $totalDevolucion = 0;
        $total = 0;
        $TPagado = 0;
        $cont = 0;
        $fechaActual = date('Y-m-d');
        
        $orderDireccion = "";
        $datareporte = $reporte->reportletras($filtro, $idzona, $idcategoriaprincipal, $idcategorias, $idvendedor, $idtipocobranza, $fechaInicio, $fechaFinal, $octavaNovena, $situacion, $fechaPagoInicio, $fechaPagoFinal, $IdCliente, $IdOrdenVenta, $orderDireccion, $tipoBanco, $recepLetras);
//
        //$dataAnterior=$datareporte[-1]['idordenventa'];
        $cantidadreporte = count($datareporte);

        echo "<table>

                                    <thead>";

        echo"<tr><th colspan='26'><h4>" . strtoupper($tipocobro) . "</h4>" . (!empty($vendedor) ? 'VENDEDOR:' . $vendedor : '') . "</th></tr>";
        echo" <tr>

                                                    <th colspan='2'style='padding:5px 30px;'>Guia</th>
                                                    <th colspan='2'style='padding:5px 10px;'>Ciudad</th>
                                                    <th colspan='2' style='padding:5px 60px;'>Cliente</th>
                                                    <th colspan='2' style='padding:5px 15px;'>Banco</th>
                                                    <th colspan='2' style='padding:5px 15px;'>Letra</th>
                                                    <th colspan='2' style='padding:5px 15px;'>Referencia</th>
                                                    <th colspan='2'style='padding:5px 15px;'>Fecha Emision</th>
                                                    <th colspan='2'style='padding:5px 15px;'>Fecha Vencimiento</th>
                                                    <th colspan='2' style='padding:5px 20px;'>Importe </th>
                                                    <th colspan='2' style='padding:5px 15px;'>Gastos Protestos</th>
                                                    <th colspan='2' style='padding:5px 30px;'>Importe Total</th>
                                                    <th colspan='2'style='padding:5px 30px;'>Pagos</th>
                                                    <th colspan='2'style='padding:5px 30px;'>Saldo</th>
                                                    <th colspan='2'style='padding:5px 15px;'>Fecha de Pago.</th>
                                            </tr>
                                    </thead>";
        echo"<tbody>";
        $dataAnterior = $datareporte[-1]['idordenventa'];
        for ($i = 0; $i < $cantidadreporte; $i++) {
            $devolucion = 0;
            if (!empty($dias)) {
                $datareporte[$i]['diffechas'] = $datareporte[$i]['diffechas'] + 10;
            }
            $simbolomoneda = $datareporte[$i]['simbolo'];
            if (strcasecmp($datareporte[$i]['situacion'], '') == 0) {
                $color = "style='color:red;text-align:right;'";
                $total += $datareporte[$i]['saldodoc'];
            } else {
                $color = "style='color:blue;text-align:right;'";
                $totalPagado += $datareporte[$i]['importedoc'] - $datareporte[$i]['saldodoc'];
            }

            if ($datareporte[$i]['referencia']) {
                //$letra=str_replace("P","",$datareporte[$i]['referencia']);
                $letra = substr($datareporte[$i]['referencia'], 0, 8);
                $importeLetra = $detalleordencobro->buscaLetra($letra);
            }
            if (!empty($datareporte[$i]['codigov'])) {
                $codigov = $datareporte[$i]['codigov'];
            }

            $pagosCredito = $detalleordencobroingreso->pagos($datareporte[$i]['iddetalleordencobro']);

            $acumulaxIdMoneda[$simbolomoneda]['acumulaSaldoDoc'] += $datareporte[$i]['importedoc'] - $pagosCredito[0]['suma'];
            $acumulaxIdMoneda[$simbolomoneda]['importedevolucion'] += $devolucion;
            $acumulaxIdMoneda[$simbolomoneda]['pagoscredito'] += $pagosCredito[0]['suma'];
            $acumulaxIdMoneda[$simbolomoneda]['importedoc'] += $datareporte[$i]['importedoc'];
            $acumulaxIdMoneda[$simbolomoneda]['montoprotesto'] += $datareporte[$i]['montoprotesto'];
            $acumulaxIdMoneda[$simbolomoneda]['importeLetra'] += $importeLetra;
            //$acumulaTotal[$simbolomoneda]['totalDeuda']=$acumulaxIdMoneda[$simbolomoneda]['importedoc']-$acumulaxIdMoneda[$simbolomoneda]['acumulaSaldoDoc'];

            $tempReferenciaFlot = $datareporte[$i]['referencia'];
            if ($datareporte[$i]['formacobro'] == 2) {
                $ultimaLetra = $tempReferenciaFlot[strlen($tempReferenciaFlot) - 1];
                if (($ultimaLetra == 'p' || $ultimaLetra == 'P') && $datareporte[$i]['situacion'] == '') {
                    $tempReferenciaFlot = '<a href="#" class="verReferencia" data-iddetalleordencobro="' . $datareporte[$i]['iddetalleordencobro'] . '"><b>' . $tempReferenciaFlot . '</b></a>';
                }
            }
            echo"<tr style='margin-botton:none;'>
                                        <td colspan='2'>" . $datareporte[$i]['codigov'] . "</td>
                                        <td colspan='2'>" . $datareporte[$i]['nombrezona'] . "</td>
                                        <td colspan='2'>" . $datareporte[$i]['razonsocial'] . "</td>
                                        <td colspan='2'>" . $datareporte[$i]['numerounico'] . "</td>
                                        <td colspan='2'>" . $datareporte[$i]['numeroletra'] . "</td>    
                                        <td colspan='2'>" . $tempReferenciaFlot . "</td>    
                                        <td colspan='2'>" . date('d/m/y', strtotime($datareporte[$i]['fechagiro'])) . "</td>
                                        <td colspan='2'>" . date('d/m/y', strtotime($datareporte[$i]['fvencimiento'])) . "</td>
                                        <td colspan='2'>" . $simbolomoneda . " " . number_format($importeLetra, 2) . "</td>
                                        <td colspan='2'>" . $simbolomoneda . " " . $datareporte[$i]['montoprotesto'] . "</td>
                                        <td colspan='2'>" . $simbolomoneda . " " . number_format($datareporte[$i]['importedoc'], 2) . "</td>
                                        <td colspan='2'>" . $simbolomoneda . " " . number_format($pagosCredito[0]['suma'], 2) . "</td>
                                        <td colspan='2'>" . $simbolomoneda . " " . number_format($datareporte[$i]['importedoc'] - $pagosCredito[0]['suma'], 2) . "</td>
                                        <td colspan='2'>" . ($datareporte[$i]['fechapago'] != '0000-00-00' ? date('d/m/y', strtotime($datareporte[$i]['fechapago'])) : "0000-00-00") . "</td>
                                        </tr>";
        }

        echo " </tbody>
                                 <tfoot>
                                        <tr>
                                            <td colspan='14'></td>
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>S/." . number_format($acumulaxIdMoneda['S/']['importeLetra'], 2) . "</strong></td >
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>S/." . number_format($acumulaxIdMoneda['S/']['montoprotesto'], 2) . "</strong></td >
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>S/. " . number_format($acumulaxIdMoneda['S/']['importedoc'], 2) . "</strong></td >
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>S/." . number_format($acumulaxIdMoneda['S/']['pagoscredito'], 2) . "</strong></td >
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>S/. " . number_format($acumulaxIdMoneda['S/']['acumulaSaldoDoc'], 2) . "<strong/></td>
                                        </tr>
                                        <tr><td colspan='14'></td>
                                            <td colspan='2'style=' border: solid 1px #0693DE;'><strong>US $" . number_format($acumulaxIdMoneda['US $']['importeLetra'], 2) . "</strong></td >
                                            <td colspan='2'style=' border: solid 1px #0693DE;'><strong>US $" . number_format($acumulaxIdMoneda['US $']['montoprotesto'], 2) . "</strong></td >
                                            <td colspan='2'style=' border: solid 1px #0693DE;'><strong>US $ " . number_format($acumulaxIdMoneda['US $']['importedoc'], 2) . "</strong></td >
                                            <td colspan='2'style=' border: solid 1px #0693DE;'><strong>US $ " . number_format($acumulaxIdMoneda['US $']['pagoscredito'], 2) . "</strong></td >
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>US $ " . number_format($acumulaxIdMoneda['US $']['acumulaSaldoDoc'], 2) . "<strong/></td>
                                        </tr>

                                         <tr>
                                            <td colspan='4'></td>
                                            <td colspan='4'></td>
                                            <td style='text-align:right;' colspan='2'></td >
                                            <td style='text-align:right;' colspan='4'></td >
                                            <th colspan='2' style='text-align:right;'>Total Deuda (S/.)</th><td colspan='2'style=' border: solid 1px #0693DE;'><strong>S/. " . number_format($acumulaxIdMoneda['S/']['acumulaSaldoDoc'], 2) . "</strong></td >
                                            <th colspan='2' style='text-align:right;'>Total Deuda (US $ )</th><td colspan='2'style=' border: solid 1px #0693DE;'><strong>US $ " . number_format($acumulaxIdMoneda['US $']['acumulaSaldoDoc'], 2) . "</strong></td >

                                          </tr>
                                 </tfoot>
                                 ";
        echo " </table>";
    }
    
    

    function cobranzageneral() {
        $this->view->show("/reporte/cobranzageneral.phtml", $data);
    }

    function reportegeneral() {
        $idtipocobro = $_REQUEST['idtipocobro'];
        $reporte = $this->AutoLoadModel('reporte');
        $detalleordencobroingreso = $this->AutoLoadModel('detalleordencobroingreso');
        if ($idtipocobro == 5) {//letras protestadas
            $filtro = "wc_detalleordencobro.`formacobro`='2' and (SUBSTRING( wc_detalleordencobro.referencia,9,1)='P' or wc_detalleordencobro.referencia='')";
            $dias = 10;
        }

        $situacion.=" and wc_detalleordencobro.`situacion`='' ";
        $datareporte = $reporte->reportletras($filtro, $idzona = "", $idcategoriaprincipal = "", $idcategorias = "", $idvendedor = "", $idtipocobranza = "", $fechaInicio = "", $fechaFinal = "", $octavaNovena = "", $situacion, $fechaPagoInicio = "", $fechaPagoFinal = "", $IdCliente = "", $IdOrdenVenta = "");
        $letrasxfirmar = $reporte->letrasxfirmar($numeroLetra = "");

        $cantidadreporte = count($datareporte);
        $cantidadletrasxfirmar = count($letrasxfirmar);


        $cont = 0;
        $cont2 = 0;
        $cont3 = 0;
        $cont4 = 0;
        for ($i = 0; $i < $cantidadreporte; $i++) {
            if ($datareporte[$i]['referencia'] != "") {
                $simbolomoneda = $datareporte[$i]['simbolo'];
                if ($datareporte[$i]['idpadrec'] == 2) {
                    if ($datareporte[$i]['nombrezona'] == 'ZONA SUR - INCOBRABLE') {
                        $totalProvZonaSur[$simbolomoneda]['SaldoProvZonaSur']+=$datareporte[$i]['saldodoc'];
                    } else {
                        $totalProv[$simbolomoneda]['SaldoProv']+=$datareporte[$i]['saldodoc'];
                        $cont+=1;
                    }
                } else {
                    $totalLima[$simbolomoneda]['SaldoLima']+=$datareporte[$i]['saldodoc'];
                    $cont2+=1;
                }
                $acumulaxIdMoneda[$simbolomoneda]['acumulaSaldoDoc']+=$datareporte[$i]['saldodoc'];
            } else {
                if ($datareporte[$i]['idpadrec'] == 2 and ( $datareporte[$i]['idactor'] != 241 or $datareporte[$i]['idactor'] != 59 or $datareporte[$i]['idactor'] != 136 or $datareporte[$i]['idactor'] != 152)) {
                    $totalProvCredito[$simbolomoneda]['SaldoProvCred']+=$datareporte[$i]['saldodoc'];
                }
                if ($datareporte[$i]['idpadrec'] == 1 and ( $datareporte[$i]['idactor'] != 241 or $datareporte[$i]['idactor'] != 59 or $datareporte[$i]['idactor'] != 136 or $datareporte[$i]['idactor'] != 152)) {
                    $totalLimaCredito[$simbolomoneda]['SaldoLimaCred']+=$datareporte[$i]['saldodoc'];
                }
            }
        }
        for ($x = 0; $x < $cantidadletrasxfirmar; $x++) {
            $simbolomoneda2 = $letrasxfirmar[$x]['simbolo'];
            if (strlen($letrasxfirmar[$x]['numeroletra']) == 8) {
                $cont3+=1;
                if (strtoupper($letrasxfirmar[$x]['recepcionletras']) == 'PA') {

                    $sumaLetraxfirmarConPA[$simbolomoneda2]['sumaLetrasxFirmarConPA']+=$letrasxfirmar[$x]['importedoc'];
                } else {

                    $sumaLetraxfirmarSinPA[$simbolomoneda2]['sumaLetrasxFirmarSinPA']+=$letrasxfirmar[$x]['importedoc'];
                }
            }
        }
        $filtro2 = "(wc_actor.`idactor`='241'  or wc_actor.`idactor`='59' or wc_actor.`idactor`='136' or wc_actor.`idactor`='152' or wc_actor.`idactor`='184') and wc_detalleordencobro.`situacion`='' and wc_detalleordencobro.`estado`=1";
        $cobranzaEmpresa = $reporte->reportclienteCobro($filtro2, $idZona = "", $idPadre = "", $idCategoria = "", $idVendedor = "", $tipoCobranza = "", $fechaInicio = "", $fechaFinal = "", $situ = "");
        $cantidadcobranzaEmpresa = count($cobranzaEmpresa);
        for ($y = 0; $y < $cantidadcobranzaEmpresa; $y++) {
            $idactor = $cobranzaEmpresa[$y]['idactor'];
            $simbolomoneda3 = $cobranzaEmpresa[$y]['simbolomoneda'];
            if ($idactor == 241) {
                $cont4+=1;
                $totalParuro_P[$simbolomoneda3]['SaldoParuro_P']+=$cobranzaEmpresa[$y]['saldodoc'];
            } elseif ($idactor == 59) {
                $totalMuestras[$simbolomoneda3]['SaldoMuestras']+=$cobranzaEmpresa[$y]['saldodoc'];
            } elseif ($idactor == 136) {
                $totalParuro_F[$simbolomoneda3]['SaldoParuro_F']+=$cobranzaEmpresa[$y]['saldodoc'];
            } elseif ($idactor == 152) {
                $totalUsoExclusivo[$simbolomoneda3]['SaldoUsoExclusivo']+=$cobranzaEmpresa[$y]['saldodoc'];
            } elseif ($idactor == 184) {
                $totalPrestamoPersonal[$simbolomoneda3]['SaldoPrestamoPersonal']+=$cobranzaEmpresa[$y]['saldodoc'];
            }
        }
        $total['totalsoles'] = $acumulaxIdMoneda['S/']['acumulaSaldoDoc'];
        $total['totaldolares'] = $acumulaxIdMoneda['US $']['acumulaSaldoDoc'];
        $total['totalProvZonaSurSoles'] = $totalProvZonaSur['S/']['SaldoProvZonaSur'];
        $total['totalProvZonaSurDolares'] = $totalProvZonaSur['US $']['SaldoProvZonaSur'];
        $total['totalProvinciaSoles'] = $totalProv['S/']['SaldoProv'];
        $total['totalProvinciaDolares'] = $totalProv['US $']['SaldoProv'];
        $total['totalLimaSoles'] = $totalLima['S/']['SaldoLima'];
        $total['totalLimaDolares'] = $totalLima['US $']['SaldoLima'];
        $total['sumaLetrasxFirmarConPAS'] = $sumaLetraxfirmarConPA['S/']['sumaLetrasxFirmarConPA'];
        $total['sumaLetrasxFirmarConPAD'] = $sumaLetraxfirmarConPA['US $']['sumaLetrasxFirmarConPA'];
        $total['sumaLetrasxFirmarSinPAS'] = $sumaLetraxfirmarSinPA['S/']['sumaLetrasxFirmarSinPA'];
        $total['sumaLetrasxFirmarSinPAD'] = $sumaLetraxfirmarSinPA['US $']['sumaLetrasxFirmarSinPA'];
        $total['totalProvCreditoS'] = $totalProvCredito['S/']['SaldoProvCred'];
        $total['totalProvCreditoD'] = $totalProvCredito['US $']['SaldoProvCred'];
        $total['totalLimaCreditoS'] = $totalLimaCredito['S/']['SaldoLimaCred'];
        $total['totalLimaCreditoD'] = $totalLimaCredito['US $']['SaldoLimaCred'];
        //empresa
        $total['totalParuro_PS'] = $totalParuro_P['S/']['SaldoParuro_P'];
        $total['totalParuro_PD'] = $totalParuro_P['US $']['SaldoParuro_P'];
        $total['totalMuestrasS'] = $totalMuestras['S/']['SaldoMuestras'];
        $total['totalMuestrasD'] = $totalMuestras['US $']['SaldoMuestras'];
        $total['totalParuro_FS'] = $totalParuro_F['S/']['SaldoParuro_F'];
        $total['totalParuro_FD'] = $totalParuro_F['US $']['SaldoParuro_F'];
        $total['totalUsoExclusivoS'] = $totalUsoExclusivo['S/']['SaldoUsoExclusivo'];
        $total['totalUsoExclusivoD'] = $totalUsoExclusivo['US $']['SaldoUsoExclusivo'];
        $total['totalPrestamoPersonalS'] = $totalPrestamoPersonal['S/']['SaldoPrestamoPersonal'];
        $total['totalPrestamoPersonalD'] = $totalPrestamoPersonal['US $']['SaldoPrestamoPersonal'];


        $total['contador'] = $cont;
        $total['contador2'] = $cont2;
        $total['contador3'] = $cont3;
        $total['contador4'] = $cont4;
        $total['cantidad'] = $cantidadcobranzaEmpresa;
        $total['TOTALLETRAS'] = $cantidadletrasxfirmar;
        echo json_encode($total);
    }

    function crearplanilla() {
        $idordenventa = (!empty($_REQUEST['idordenventa']) ? $_REQUEST['idordenventa'] : '');
        $numeroLetra = $_REQUEST['numeroLetra'];
        $reporte = $this->AutoLoadModel('reporte');
        $data = $reporte->letrasxfirmar($numeroLetra, $idordenventa);
        $cadena = $data[0]['ruc'];
        $resultado['rucx'] = $data[0]['ruc'];
        $resultado['diaHoy'] = date('d/m/Y');
        if($data[0]['iddepartamento'] == 14 || $data[0]['iddepartamento'] == 24){
            $resultado['plaza'] = "LIMA";
        }else{
            $resultado['plaza'] = "PROVINCIA";
        }
        if (empty($cadena)) {
            $resultado['tipodoc'] = $data[0]['dni'];
            $resultado['doc'] = 1;
        } else {
            if (substr($cadena, 0, 1) == 2) {
                $resultado['tipodoc'] = $data[0]['ruc'];
                $resultado['doc'] = 6;
            } else {
                $resultado['tipodoc'] = $data[0]['dni'];
                $resultado['doc'] = 1;
            }
        }
        if ($data[0]['nombrecli'] == "" && $data[0]['apellido1'] == "" && $data[0]['apellido2'] == "") {
            $resultado['nombrecli'] = $data[0]['razonsocial'];
            $resultado['apellido1'] = "";
            $resultado['apellido2'] = "";
        } else {
            $resultado['nombrecli'] = $data[0]['nombrecli'];
            $resultado['apellido1'] = $data[0]['apellido1'];
            $resultado['apellido2'] = $data[0]['apellido2'];
        }
        $resultado['importedoc'] = $data[0]['importedoc'];
        $resultado['simbolo'] = $data[0]['simbolo'];
        if($data[0]['idmoneda'] == 2){
            $resultado['monedita'] = "DOL";
        }else if($data[0]['idmoneda'] == 1){
            $resultado['monedita'] = "SOL";
        }else{
            $resultado['monedita'] = $data[0]['simbolo'];
        }
        $resultado['numeroletra'] = $data[0]['numeroletra'];
        $resultado['fvencimiento'] = $this->FechaLetra($data[0]['fvencimiento']);
        $resultado['fvencimiento2'] = date('d/m/Y',strtotime($data[0]['fvencimiento']));
        $resultado['iddetalleordencobro'] = $data[0]['iddetalleordencobro'];
        //$resultado['plaza'] = "PROVINCIA";
        echo json_encode($resultado);
    }


    function actualizaCampo() {
        $numeroLetra = $_REQUEST['numeroLetra'];
        $valor = $_REQUEST['valor'];
        $reporte = $this->AutoLoadModel('reporte');
        $data = $reporte->updateCampo($numeroLetra, $valor);
        echo json_encode($data);
    }

    function reporteCreditos() {
        $reporte = $this->AutoLoadModel('reporte');
        $tipo = $this->AutoLoadModel('tipocobranza');
        $idzona = $_REQUEST['idzona'];
        $idcategoriaprincipal = $_REQUEST['idcategoriaprincipal'];
        $idcategoria = $_REQUEST['idcategoria'];
        $idvendedor = $_REQUEST['idvendedor'];
        $idtipocobro = $_REQUEST['idtipocobro'];
        $idtipocobranza = $_REQUEST['idtipocobranza'];
        $fechaInicio = $_REQUEST['fechaInicio'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        $situacion = $_REQUEST['situacion'];
        $pendiente = $_REQUEST['pendiente'];
        $cancelado = $_REQUEST['cancelado'];
        $octava = $_REQUEST['octava'];
        $novena = $_REQUEST['novena'];

        $octavaNovena = "";

        $situacion = "";
        if (!empty($pendiente) && !empty($cancelado)) {
            $situacion.=" and (wc_detalleordencobro.`situacion`='' or wc_detalleordencobro.`situacion`='cancelado') ";
        } elseif (!empty($cancelado)) {
            $situacion.=" and wc_detalleordencobro.`situacion`='cancelado' ";
        } elseif (!empty($pendiente)) {
            $situacion.=" and wc_detalleordencobro.`situacion`='' ";
        }
        if ($_REQUEST['fechaInicio'] != "") {
            $fechaInicio = date('Y-m-d', strtotime($fechaInicio));
        }

        if ($_REQUEST['fechaFinal'] != "") {
            $fechaFinal = date('Y-m-d', strtotime($fechaFinal));
        }
        $titulo = $_REQUEST['titulo'];


        if (!empty($idtipocobranza)) {
            $dataTipoCobranza = $tipo->buscaxid($idtipocobranza);
            $titulo2 = $dataTipoCobranza[0]['nombre'];
        } else {
            $titulo2 = 'Todo';
        }
        if ($idtipocobro == 1) {
            $filtro = "wc_detalleordencobro.`formacobro`='1' ";
        } elseif ($idtipocobro == 2) {
            $filtro = "wc_detalleordencobro.`formacobro`='2' ";
        }


        $total = 0;
        $gastosrenovacion = 0;

        $datareporte = $reporte->reportletras($filtro, $idzona, $idcategoriaprincipal, $idcategoria, $idvendedor, $idtipocobranza, $fechaInicio, $fechaFinal, $octavaNovena, $situacion);

        echo "<thead>
                                 <tr>
                                 <tr>
                                        <th colspan=6>" . $titulo . "</th>
                                        <th colspan=5>" . $titulo2 . "</th>
                                 </tr>
                                         <th>Vend." . $idvendedor . "</th>
                                         <th>Orden Venta</th>
                                         <th>Z. Cobranza</th>
                                         <th>Zona</th>
                                         <th>F. Giro</th>
                                         <th>F. Vencimiento</th>
                                         <th>Cliente</th>
                                         <th>Importe</th>
                                         <th>Pago</th>
                                         <th>Saldo</th>
                                         <th>Situacion</th>
                                 </tr>
                                 </thead>
                                 <tbody>";
        $cantidadreporte = count($datareporte);
        for ($i = 0; $i < $cantidadreporte; $i++) {
            if (strcasecmp($datareporte[$i]['situacion'], '') == 0) {
                $color = "style='color:red;text-align:right;'";
                $total+=$datareporte[$i]['saldodoc'];
            } else {
                $color = "style='color:red;text-align:right;'";
            }
            if ($datareporte[$i]['gastosrenovacion'] == 1) {
                $gastosrenovacion+=$datareporte[$i]['importedoc'];
            }
            echo "<tr>
                                                <td>" . $datareporte[$i]['idactor'] . "</td>
                                                <td>" . $datareporte[$i]['codigov'] . "</td>
                                                <td>" . $datareporte[$i]['nombrec'] . "</td>
                                                <td>" . $datareporte[$i]['nombrezona'] . "</td>
                                                <td>" . $datareporte[$i]['fechagiro'] . "</td>
                                                <td>" . $datareporte[$i]['fvencimiento'] . "</td>
                                                <td>" . $datareporte[$i]['razonsocial'] . "</td>
                                                <td>S/. " . number_format($datareporte[$i]['importedoc'], 2) . "</td>
                                                <td>S/. " . number_format(($datareporte[$i]['importedoc'] - $datareporte[$i]['saldodoc']), 2) . "</td>
                                                <td " . $color . ">S/. " . number_format($datareporte[$i]['saldodoc'], 2) . "</td>
                                                <td>" . ($datareporte[$i]['situacion'] == '' ? 'Pendiente' : $datareporte[$i]['situacion']) . "</td>
                                         </tr>";
        }

        echo "</tbody>
                                 <tfoot>
                                        <tr>
                                                <td colspan='3'>&nbsp</td>
                                                <th>Deuda Pendiente</th>
                                                <td>S/. " . number_format($total, 2) . "</td>
                                                <td>&nbsp</td>
                                        </tr>
                                 </tfoot>
                                 ";
    }
    
    function verdocLetraprotestada() {
        set_time_limit(1000);
        $reporte = $this->AutoLoadModel('reporte');
        $tipo = $this->AutoLoadModel('tipocobranza');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $tipoCobroIni = $this->configIniTodo('TipoCobro');
        $movimiento = $this->AutoLoadModel('movimiento');
        $detalleordencobro = $this->AutoLoadModel('detalleordencobro');
        $detalleordencobroingreso = $this->AutoLoadModel('detalleordencobroingreso');
        
        $iddetalleordencobro = $_REQUEST['iddetalleordencobro'];
        $situacion .= " and wc_detalleordencobro.`situacion`='' ";
        $filtro = "wc_detalleordencobro.`formacobro`='2' and (substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p')";
        $filtro .= "and wc_zona.`nombrezona` not like '%incobrab%'";
        $dias = 10;

        $TOTALIMPORTE2 = 0;

        $totalPagado = 0;
        $totalImporte = 0;
        $importe = 0;
        $totalDevolucion = 0;
        $total = 0;
        $TPagado = 0;
        $cont = 0;
        $fechaActual = date('Y-m-d');
        $datareporte = $reporte->reportletras_verprotestopordetalleoc($filtro, $situacion, $iddetalleordencobro);
//
        //$dataAnterior=$datareporte[-1]['idordenventa'];
        $cantidadreporte = count($datareporte);

        echo "<table>";

                                    
        //$dataAnterior = $datareporte[-1]['idordenventa'];
        if ($cantidadreporte > 0) {
            echo "<thead>";
            echo"<tr style='margin-botton:none;'>
                    <th colspan='2'style='padding:5px 30px;'>Guia</th>
                    <td colspan='2'>" . $datareporte[0]['codigov'] . "</td>
                    <th colspan='2'style='padding:5px 10px;'>Ciudad</th>
                    <td colspan='2'>" . $datareporte[0]['nombrezona'] . "</td>
                    <th colspan='2' style='padding:5px 60px;'>Cliente</th>
                    <td colspan='5'>" . $datareporte[0]['razonsocial'] . "</td>
                </tr>";
            echo" <tr>
                            <th colspan='2'style='padding:5px 15px;'>Fecha Emision</th>
                            <th colspan='2'style='padding:5px 15px;'>Fecha Vencimiento</th>
                            <th colspan='2' style='padding:5px 20px;'>Importe </th>
                            <th colspan='2' style='padding:5px 15px;'>Gastos Protestos</th>
                            <th colspan='2' style='padding:5px 30px;'>Importe Total</th>
                            <th colspan='2'style='padding:5px 30px;'>Pagos</th>
                            <th colspan='2'style='padding:5px 30px;'>Saldo</th>
                            <th colspan='2'style='padding:5px 15px;'>Fecha de Pago.</th>
                    </tr>
            </thead>
            <tbody>";

        
            $devolucion = 0;
            if (!empty($dias)) {
                $datareporte[0]['diffechas'] = $datareporte[0]['diffechas'] + 10;
            }
            $simbolomoneda = $datareporte[0]['simbolo'];
            if (strcasecmp($datareporte[0]['situacion'], '') == 0) {
                $color = "style='color:red;text-align:right;'";
                $total += $datareporte[0]['saldodoc'];
            } else {
                $color = "style='color:blue;text-align:right;'";
                $totalPagado += $datareporte[0]['importedoc'] - $datareporte[0]['saldodoc'];
            }

            if ($datareporte[0]['referencia']) {
                //$letra=str_replace("P","",$datareporte[0]['referencia']);
                $letra = substr($datareporte[0]['referencia'], 0, 8);
                $importeLetra = $detalleordencobro->buscaLetra($letra);
            }
//            if (!empty($datareporte[0]['codigov'])) {
//                $codigov = $datareporte[0]['codigov'];
//            }

            $pagosCredito = $detalleordencobroingreso->pagos($datareporte[0]['iddetalleordencobro']);

            $acumulaxIdMoneda[$simbolomoneda]['acumulaSaldoDoc'] += $datareporte[0]['importedoc'] - $pagosCredito[0]['suma'];
            $acumulaxIdMoneda[$simbolomoneda]['importedevolucion'] += $devolucion;
            $acumulaxIdMoneda[$simbolomoneda]['pagoscredito'] += $pagosCredito[0]['suma'];
            $acumulaxIdMoneda[$simbolomoneda]['importedoc'] += $datareporte[0]['importedoc'];
            $acumulaxIdMoneda[$simbolomoneda]['montoprotesto'] += $datareporte[0]['montoprotesto'];
            $acumulaxIdMoneda[$simbolomoneda]['importeLetra'] += $importeLetra;
            //$acumulaTotal[$simbolomoneda]['totalDeuda']=$acumulaxIdMoneda[$simbolomoneda]['importedoc']-$acumulaxIdMoneda[$simbolomoneda]['acumulaSaldoDoc'];


            echo"<tr>
                    <td colspan='2'>" . date('d/m/y', strtotime($datareporte[0]['fechagiro'])) . "</td>
                    <td colspan='2'>" . date('d/m/y', strtotime($datareporte[0]['fvencimiento'])) . "</td>
                    <td colspan='2'>" . $simbolomoneda . " " . number_format($importeLetra, 2) . "</td>
                    <td colspan='2'>" . $simbolomoneda . " " . $datareporte[0]['montoprotesto'] . "</td>
                    <td colspan='2'>" . $simbolomoneda . " " . number_format($datareporte[0]['importedoc'], 2) . "</td>
                    <td colspan='2'>" . $simbolomoneda . " " . number_format($pagosCredito[0]['suma'], 2) . "</td>
                    <td colspan='2'>" . $simbolomoneda . " " . number_format($datareporte[0]['importedoc'] - $pagosCredito[0]['suma'], 2) . "</td>
                    <td colspan='2'>" . ($datareporte[0]['fechapago'] != '0000-00-00' ? date('d/m/y', strtotime($datareporte[0]['fechapago'])) : "0000-00-00") . "</td>
                </tr>";
        }

        echo " </tbody>
                                 <tfoot>
                                        <tr>
                                            <td colspan='4'></td>
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>S/." . number_format($acumulaxIdMoneda['S/']['importeLetra'], 2) . "</strong></td >
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>S/." . number_format($acumulaxIdMoneda['S/']['montoprotesto'], 2) . "</strong></td >
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>S/. " . number_format($acumulaxIdMoneda['S/']['importedoc'], 2) . "</strong></td >
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>S/." . number_format($acumulaxIdMoneda['S/']['pagoscredito'], 2) . "</strong></td >
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>S/. " . number_format($acumulaxIdMoneda['S/']['acumulaSaldoDoc'], 2) . "<strong/></td>
                                        </tr>
                                        <tr><td colspan='4'></td>
                                            <td colspan='2'style=' border: solid 1px #0693DE;'><strong>US $" . number_format($acumulaxIdMoneda['US $']['importeLetra'], 2) . "</strong></td >
                                            <td colspan='2'style=' border: solid 1px #0693DE;'><strong>US $" . number_format($acumulaxIdMoneda['US $']['montoprotesto'], 2) . "</strong></td >
                                            <td colspan='2'style=' border: solid 1px #0693DE;'><strong>US $ " . number_format($acumulaxIdMoneda['US $']['importedoc'], 2) . "</strong></td >
                                            <td colspan='2'style=' border: solid 1px #0693DE;'><strong>US $ " . number_format($acumulaxIdMoneda['US $']['pagoscredito'], 2) . "</strong></td >
                                            <td colspan='2' style=' border: solid 1px #0693DE;'><strong>US $ " . number_format($acumulaxIdMoneda['US $']['acumulaSaldoDoc'], 2) . "<strong/></td>
                                        </tr>

                                         <tr>
                                            <td colspan='2'></td>
                                            <td style='text-align:right;' colspan='2'></td >
                                            <td style='text-align:right;' colspan='2'></td >
                                            <th colspan='2' style='text-align:right;'>Total Deuda (S/.)</th><td colspan='2'style=' border: solid 1px #0693DE;'><strong>S/. " . number_format($acumulaxIdMoneda['S/']['acumulaSaldoDoc'], 2) . "</strong></td >
                                            <th colspan='2' style='text-align:right;'>Total Deuda (US $ )</th><td colspan='2'style=' border: solid 1px #0693DE;'><strong>US $ " . number_format($acumulaxIdMoneda['US $']['acumulaSaldoDoc'], 2) . "</strong></td >

                                          </tr>
                                 </tfoot>
                                 ";
        echo " </table>";
    }

    function letraxCliente() {
        $reporte = $this->AutoLoadModel('reporte');
        $detalleordencobro = $this->AutoLoadModel('detalleordencobro');
        $idtipocobro = $_REQUEST['tipoCobro'];
        $fechaInicio = $_REQUEST['fechaInicio'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        $situacion = $_REQUEST['situacion'];
        $idcliente = $_REQUEST['idcliente'];

        if ($_REQUEST['fechaInicio'] != "") {
            $fechaInicio = date('Y-m-d', strtotime($_REQUEST['fechaInicio']));
        }
        $fechaFinal = $_REQUEST['fechaFinal'];
        if ($_REQUEST['fechaFinal'] != "") {
            $fechaFinal = date('Y-m-d', strtotime($_REQUEST['fechaFinal']));
        }
        if (empty($idtipocobro)) {
            $formacobro = '';
            $filtro = " wc_cliente.`idcliente`='$idcliente' ";
        } elseif ($idtipocobro == 1) {

            $filtro = "wc_detalleordencobro.`formacobro`='1' and wc_cliente.`idcliente`='$idcliente' ";
        } elseif ($idtipocobro == 2) {

            $filtro = "wc_detalleordencobro.`formacobro`='2' and wc_cliente.`idcliente`='$idcliente' ";
        } elseif ($idtipocobro == 3) {

            $filtro = "wc_detalleordencobro.`formacobro`='3' and wc_ordencobro.`tipoletra`=1 and wc_cliente.`idcliente`='$idcliente' ";
        } elseif ($idtipocobro == 4) {

            $filtro = "wc_detalleordencobro.`formacobro`='3' and  wc_ordencobro.`tipoletra`=2 and wc_cliente.`idcliente`='$idcliente' ";
        }


        $total = 0;
        if (strcasecmp($situacion, 'pendiente') == 0) {
            $situacion = " and wc_detalleordencobro.`situacion`=''";
        } elseif (strcasecmp($situacion, 'cancelado') == 0) {
            $situacion = " and wc_detalleordencobro.`situacion`='cancelado'";
        }

        $datareporte = $reporte->reportclienteCobro($filtro, "", "", "", "", "", $fechaInicio, $fechaFinal, $situacion);

        // echo "<pre>";
        // print_r($datareporte);
        // exit;
        echo "<thead>
                                 <tr>
                                         <th>Vend." . $idvendedor . "</th>
                                         <th width='80px'>Orden Venta</th>
                                         <th>Observaciones</th>
                                         <th>Z. Cobranza</th>
                                         <th>Zona</th>
                                         <th width='80px'>F. Giro</th>
                                         <th width='80px'>F. Vencimiento</th>
                                         <th width='120px'>Tipo Cobro</th>
                                         <th width='40px'>Estado</th>

                                         <th>Numero Letra</th>
                                         <th>Referencia</th>
                                         <th width='100px'>Importe</th>
                                         <th width='100px'>Saldo</th>
                                         <th>Situacion</th>
                                 </tr>
                                 </thead>
                                 <tbody>";
        $cantidadreporte = count($datareporte);
        for ($m = 0; $m < $cantidadreporte; $m++) {
            $datareporteOV[] = $datareporte[$m]['codigov'];
            $contarOV = array_count_values($datareporteOV);
        }
        $OVactual = "";

        for ($i = 0; $i < $cantidadreporte; $i++) {
            $tempReferencia = $datareporte[$i]['referencia'];
            $tempImporteLetra = '';
            $tempGP = '';
            if ($datareporte[$i]['formacobro'] == 1) {
                $formacobro = "Contado";
            } elseif ($datareporte[$i]['formacobro'] == 2) {
                $formacobro = "Credito";
                $ultimaLetra = $tempReferencia[strlen($tempReferencia) - 1];
                if (($ultimaLetra == 'p' || $ultimaLetra == 'P') && $datareporte[$i]['situacion'] == '') {
                    $letra = substr($datareporte[$i]['referencia'], 0, 8);
                    $tempImporteLetra = '<b>Importe Letra:</b><br><span style="color: blue">' . $datareporte[$i]['simbolomoneda'] . " " . number_format($detalleordencobro->buscaLetra($letra), 2). '</span>';
                    $tempGP = '<b>Gasto Protesto:</b><br><span style="color: blue">' . $datareporte[$i]['simbolomoneda'] . " " . number_format($datareporte[$i]['montoprotesto'], 2) . '</span>';
                    $tempReferencia = '<a href="#" class="verReferencia" data-iddetalleordencobro="' . $datareporte[$i]['iddetalleordencobro'] . '"><b>' . $tempReferencia . '</b></a>';
                }
            } elseif ($datareporte[$i]['formacobro'] == 3) {
                $formacobro = 'Letra Banco';
            } elseif ($datareporte[$i]['formacobro'] == 4) {
                $formacobro = 'Letra Cartera';
            }

            if (strcasecmp($datareporte[$i]['situacion'], '') == 0) {
                $color = "style='color:red;text-align:right;'";
                $total+=$datareporte[$i]['saldodoc'];
            } else {
                $color = "style='color:blue;text-align:right;'";
            }
            $acumula[$datareporte[$i]['simbolomoneda']]['importedoc']+=$datareporte[$i]['importedoc'];
            $acumula[$datareporte[$i]['simbolomoneda']]['saldodoc']+=$datareporte[$i]['saldodoc'];
            $OV = $datareporte[$i]['codigov'];
            if ($OV != $OVactual) {
                $rowspan = "rowspan=" . $contarOV[$OV];
                $OVactual = $OV;
            } else {
                $rowspan = "";
            }


            echo "<tr >";
            if (!empty($rowspan)) {
                echo "<td " . $rowspan . ">" . $datareporte[$i]['idactor'] . "</td>";
            }
            if (!empty($rowspan)) {
                echo "<td " . $rowspan . ">" . $datareporte[$i]['codigov'] . "</td>";
            }
            if (!empty($rowspan)) {
                echo "<td " . $rowspan . ">" . htmlspecialchars_decode($datareporte[$i]['observaciones']) . "</td>";
            }
            if (!empty($rowspan)) {
                echo "<td " . $rowspan . ">" . $datareporte[$i]['nombrec'] . "</td>";
            }
            if (!empty($rowspan)) {
                echo "<td " . $rowspan . ">" . $datareporte[$i]['nombrezona'] . "</td>";
            }
            
            echo "
                                                <td>" . $datareporte[$i]['fechagiro'] . "</td>
                                                <td>" . $datareporte[$i]['fvencimiento'] . "</td>
                                                <td>" . $formacobro . "</td>";
            if (empty($tempImporteLetra) && empty($tempGP)) {
                echo "<td>" . $datareporte[$i]['recepcionletras'] . (!empty($datareporte[$i]['numerounico']) ? ' <b>[' . $datareporte[$i]['numerounico'] . ']</b>' : '') . "</td>
                     <td>" . $datareporte[$i]['numeroletra'] . "</td>";
            } else {
                echo "<td>" . $tempImporteLetra . "</td>
                      <td>" . $tempGP . "</td>";
            }
            
            echo                                "<td>" . $tempReferencia . "</td>
                                                <td " . $color . ">" . $datareporte[$i]['simbolomoneda'] . " " . number_format($datareporte[$i]['importedoc'], 2) . "</td>
                                                <td " . $color . ">" . $datareporte[$i]['simbolomoneda'] . " " . number_format($datareporte[$i]['saldodoc'], 2) . "</td>
                                                <td>" . ($datareporte[$i]['situacion'] == '' ? 'Pendiente' : $datareporte[$i]['situacion']) . "</td>
                                         </tr>";
        }

        echo "</tbody>
                        <tfoot>
                                <tr><td colspan='7'>&nbsp</td><th>Deuda Pendiente en S/.  :</th><td style='text-align:right;'> S/. " . number_format($acumula['S/']['saldodoc'], 2) . "</td><td>&nbsp</td></tr>
                                <tr><td colspan='7'>&nbsp</td><th>Deuda Pendiente en US $ :</th><td style='text-align:right;'>US $ " . number_format($acumula['US $']['saldodoc'], 2) . "</td><td>&nbsp</td></tr>
                        </tfoot>";
    }

    function comisionVendedor() {

        $this->view->show("/reporte/comisionVendedor.phtml", $data);
    }

    function reporteCliente() {
        $this->view->show('/reporte/reporteCliente.phtml', $data);
    }

    function reporteingresos() {
        $data['tipoIngreso'] = $this->configIniTodo('TipoIngreso');
        $this->view->show('/reporte/reporteingresos.phtml', $data);
    }

    function reporteVentas() {
        $zona = $this->AutoLoadModel('zona');
        $data['categoriaPrincipal'] = $zona->listaCategoriaPrincipal();
        $data['condicionVenta'] = $this->configIniTodo('TipoCobro');
        $this->view->show('/reporte/reporteventas.phtml', $data);
    }

    function reporteVentasXdia() {
        $zona = $this->AutoLoadModel('zona');
        $data['categoriaPrincipal'] = $zona->listaCategoriaPrincipal();
        $data['condicionVenta'] = $this->configIniTodo('TipoCobro');
        $this->view->show('/reporte/reporteventasxdia.phtml', $data);
    }

    function reporteProductos() {
        $this->view->show('/reporte/reporteProductos.phtml', $data);
    }

    function reporteInventario() {
        $inventario = $this->AutoLoadModel('inventario');
        $bloques = $this->AutoLoadModel('bloques');
        $data['inventario'] = $inventario->listadoConFecha();
        $data['bloques'] = $bloques->listado();
        $linea = new Linea();
        $data['Linea'] = $linea->listadoLineas('idpadre=0');
        $this->view->show('/reporte/reporteInventario.phtml', $data);
    }

    function reporteCobranza() {
        $actor = $this->AutoLoadModel('actorrol');
        $zona = $this->AutoLoadModel('zona');
        $tipo = $this->AutoLoadModel('tipocobranza');
        $data['vendedor'] = $actor->actoresxRolxNombreSinconEstado(25);
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['tipocobranza'] = $tipo->lista();
        $this->view->show('/reporte/reporteCobranza.phtml', $data);
    }

    function kardexTotalxProducto() {
        $data['mes'] = $this->meses();
        $this->view->show("/reporte/kardexTotalxProducto.phtml", $data);
    }

    function reporteOrdenCompra() {
        $this->view->show('/reporte/reporteOrdenCompra.phtml', $data);
    }

    function reporteCarteraClientes() {
        $zona = $this->AutoLoadModel('zona');
        $linea = $this->AutoLoadModel('linea');
        $data['linea'] = $linea->listaLineas();
        $data['categoriaPrincipal'] = $zona->listaCategoriaPrincipal();
        $data['condicionVenta'] = $this->configIniTodo('TipoCobro');
        //$data['situacion']=$this->configIniTodo('SituacionVenta');
        $this->view->show('/reporte/reporteCarteraClientes.phtml', $data);
    }

    function reporteCarteraClientes_seleccion() {

        set_time_limit(500);
        //$linprod = $_REQUEST['lstLineaProductos'];
        $idvend = $_REQUEST['idVendedor'];
        $condicion = $_REQUEST['lstCondicion'];
        $catprin = $_REQUEST['lstCategoriaPrincipal'];
        $regcobr = $_REQUEST['lstRegionCobranza'];
        $zona = $_REQUEST['lstZona'];
        $fecini = $_REQUEST['txtFechaInicio'];
        $fecfin = $_REQUEST['txtFechaFin'];
        $depa = $_REQUEST['lstDepartamento'];
        $prov = $_REQUEST['lstProvincia'];
        $dist = $_REQUEST['lstDistrito'];
        $ordenar = $_REQUEST['lstOrden'];
        $mostrar = $_REQUEST['lstMostrar'];
        $aprobados = $_REQUEST['aprobados'];

        $cliente=New Cliente();

        $cartcli = new CarteraCliente($idvend, $condicion, $catprin, $regcobr, $zona, $fecini, $fecfin, $depa, $prov, $dist, $ordenar, $aprobados);
        $datos = $cartcli->listarCartera();
        $cantidadData = count($datos);
        echo "<form action='' target='_blank' id='form-seleccion' method='post'>";
             echo "<input type='hidden' name='idVendedor' value='".$idvend."'>";
             echo "<input type='hidden' name='lstCondicion' value='".$condicion."'>";
             echo "<input type='hidden' name='lstCategoriaPrincipal' value='".$catprin."'>";
             echo "<input type='hidden' name='lstRegionCobranza' value='".$regcobr."'>";
             echo "<input type='hidden' name='lstZona' value='".$zona."'>";
             echo "<input type='hidden' name='txtFechaInicio' value='".$fecini."'>";
             echo "<input type='hidden' name='txtFechaFin' value='".$fecfin."'>";
             echo "<input type='hidden' name='lstDepartamento' value='".$depa."'>";
             echo "<input type='hidden' name='lstProvincia' value='".$prov."'>";
             echo "<input type='hidden' name='lstDistrito' value='".$dist."'>";
             echo "<input type='hidden' name='lstOrden' value='".$ordenar."'>";
             echo "<input type='hidden' name='lstMostrar' value='".$mostrar."'>";
             echo "<input type='hidden' name='lstAprobados' value='".$aprobados."'>";
        if (!empty($idvend)) {
            $vendedor = new Actor();
            $reg = $vendedor->buscarxid($idvend);
            echo "<h3>VENDEDOR: ".$reg[0]['nombres']." ".$reg[0]['apellidopaterno'] . "</h3>";
        }        else {
            echo "<h3>CARTERA DE CLIENTES</h3>";
        }
        $fecha = "";
        if (!empty($fecini)) {
            $fecha .= "GUIAS DESDE ".$fecini;
        } else {
            $fecha .= "<b>TODAS LAS GUIAS</b>";
        }
        $fecha.=" HASTA ";
        if (!empty($fecfin)) {
            $fecha .= $fecfin;
        } else {
            $fecha = '';
        }
        $cond2 = "";
        if ($aprobados == 2) $cond2 = " - APROBADOS" ;
        else if ($aprobados == 3) $cond2 = " - PENDIENTES" ;
        else if ($aprobados == 4) $cond2 = " - DESAPROBADOS" ;
        echo $fecha . "<br>";
        echo "<b>FECHA IMPRESION:</b> " . date('Y/m/d') . "<br><hr><br>";
        echo "<b>CONDICION: </b>".(empty($condicion) ? "TODOS" : ($condicion==1 ? 'CONTADO' : ($condicion==2 ? "CREDITO" : ($condicion==3 ? 'LETRAS BANCO' : 'LETRAS CARTERA')))). $cond2 . "<br><br>";
        echo "<b><input type='checkbox' id='chkTodo' checked> TODOS LAS ZONAS</b><br><br>";
        $zona = "";
        $total = 0.0;
        $cant = 0;

        $linea = new Linea();
        $ordenventa = $this->AutoLoadModel('ordenventa');

        for ($i = 0; $i < $cantidadData; $i++) {
            $datosOrden = $ordenventa->detalleOrden($datos[$i]['idcliente'], 2);
            $datosOrden2 = $ordenventa->detalleOrden($datos[$i]['idcliente'], 1);
            $tienedeuda = (($datosOrden['deuda'] >=1.0) || ($datosOrden2['deuda'] >=1.0));
            if ((empty($mostrar))||($mostrar == 'D' && $tienedeuda)||($mostrar == 'N' && !$tienedeuda)) {
                $tot=0;
                if ($datosOrden['sumtotal']<500 || $datosOrden2['sumtotal']<900) {
                    $tot += 3;
                } elseif (($datosOrden['sumtotal']>=500 && $datosOrden['sumtotal']<2000) || ($datosOrden2['sumtotal']>=900 && $datosOrden2['sumtotal']<4000)) {
                    $tot += 2;
                } else {
                    $tot += 1;
                }

                if ($datosOrden['diasmora']<5 || $datosOrden2['diasmora']<5) {
                    $tot += 1;
                } elseif (($datosOrden['diasmora']>=5 && $datosOrden['diasmora']<90) || ($datosOrden2['diasmora']>=5 && $datosOrden2['diasmora']<90)) {
                    $tot += 2;
                } else {
                    $tot += 3;
                }
                $datosOrden['calif'] = $tot;
                if ($zona != $datos[$i]['nombrezona']) {
                    if ($i != 0) echo "</tbody></table>";
                    $zona = $datos[$i]['nombrezona'];
                    echo "<b><input type='checkbox' class='chkzona' name='zonaSelec[]' value='".$datos[$i]['idzona']."' checked> ZONA: ".$datos[$i]['nombrezona']."</b><br>";
                    echo "<table>";
                        echo "<thead>";
                            echo "<tr>";
                                echo "<th>N</th>";
                                echo "<th>CLIENTE</th>";
                                echo "<th>RUC</th>";
                                echo "<th>TELF.</th>";
                                echo "<th>DIRECCION</th>";
                                echo "<th>EMAIL</th>";
                                echo "<th>ULT. GUIA</th>";
                                echo "<th>FECHA GUIA</th>";
                                echo "<th>PROM.COMPRA</th>";
                                echo "<th>MAX.MORA</th>";
                                echo "<th>DEUDA</th>";
                                echo "<th>CALIFICACION</th>";
                                echo "<th>LINEA CREDITO</th>";
                                echo "<th>CALIFICACION CRÉDITO</th>";
                                echo "<th>OBSERVACIÓN</th>";
                                echo "<th>LINEA DE CRÉDITO DISPONIBLE</th>";
                            echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                }
                //inicio nuevo
                //echo "<br>".$datos[$i]['idcliente']."<br>";
                $dataPosicionCliente=$cliente->detalleposicion($datos[$i]['idcliente']);
                //$var_dump($dataPosicionCliente);
                $tamanio=count($dataPosicionCliente);
                //echo $tamanio;
                for ($j=0; $j < $tamanio; $j++) {
                    $lineaCredito = $dataPosicionCliente[$j]['lineacredito'];
                    $observacionCredito = $dataPosicionCliente[$j]['observacion'];

                    switch ($dataPosicionCliente[$j]['calificacion']) {
                        case '1': $formacobro="Cliente A1"; break;
                        case '2': $formacobro="Buen cliente"; break;
                        case '3': $formacobro="Cliente en Observación"; break;
                        case '4': $formacobro="Cliente moroso"; break;
                        case '5': $formacobro="Cliente incobrable"; break;
                    }
                }
                //fin nuevo
                echo "<tr>";
                    echo "<td rowspan='2'>".($i+1)."</td>";
                    echo "<td rowspan='2'>".$datos[$i]['cliente']."</td>";
                    echo "<td rowspan='2'>".$datos[$i]['ruc']."</td>";
                    echo "<td rowspan='2'>".$datos[$i]['telefono'] . (empty($datos[$i]['telefono']) || empty($datos[$i]['celular']) ? "" : " / ") . $datos[$i]['celular']."</td>";
                    echo "<td rowspan='2'>".$datos[$i]['direccion'] . ", " .$datos[$i]['dist'] . " - " . $datos[$i]['prov'] . " - " . $datos[$i]['depa']."</td>";
                    echo "<td rowspan='2'>".$datos[$i]['email']."</td>";
                    echo "<td rowspan='2'>".$datos[$i]['codigov']."</td>";
                    echo "<td rowspan='2'>".$datos[$i]['fordenventa']."</td>";
                    echo "<td>US$ ".round($datosOrden['sumtotal'], 2)."</td>";
                    echo "<td>".round($datosOrden['diasmora'])."</td>";
                    echo "<td>".round($datosOrden['deuda'], 2)."</td>";
                    echo "<td>".($datosOrden['calif']==2 || $datosOrden['calif']==3 ? 'BUENO' : ($datosOrden['calif']==4 ? 'REGULAR' : 'CLASE C'))."</td>";
                    echo "<td>".$lineaCredito."</td>";
                    echo "<td>".$formacobro."</td>";
                    echo "<td>".$observacionCredito."</td>";
                    echo "<td>".round($lineaCredito-$datosOrden['deuda'],2)."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>S/. ".round($datosOrden2['sumtotal'], 2)."</td>";
                    echo "<td>".round($datosOrden2['diasmora'])."</td>";
                    echo "<td>".round($datosOrden2['deuda'], 2)."</td>";
                    echo "<td>".($datosOrden2['calif']==2 || $datosOrden2['calif']==3 ? 'BUENO' : ($datosOrden2['calif']==4 ? 'REGULAR' : 'CLASE C'))."</td>";

                echo "</tr>";
            }
        }
        if ($i != 0) echo "</tbody></table>";
        echo "<hr>";//form-seleccion
        echo '<div align="right">'
                . '<button id="btnSelExcel">Consultar <img style="vertical-align:middle" src="/imagenes/excel.png" width="25" height="25" /></button>'
                . '<button id="btnSelPDF">Consultar <img style="vertical-align:middle" src="/imagenes/iconos/pdf.gif" width="25" height="25" /></button>'
                . '</div>';
        echo "</form>";
    }

    function reporteHistorialVentasxProducto() {
        $this->view->show('/reporte/historialVentasxProducto.phtml', $data);
    }

    function reporteCobranzaxEmpresa() {
        $zona = $this->AutoLoadModel('zona');
        $tipo = $this->AutoLoadModel('tipocobranza');
        $empresa = $this->AutoLoadModel('almacen');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['tipocobranza'] = $tipo->lista();
        $data['empresa'] = $empresa->listado();
        $this->view->show('/reporte/reportecobranzaxempresa.phtml', $data);
    }

    function consultacostos(){
        $mes = $_REQUEST['opcmes'];
        $anho = $_REQUEST['opcanho'];
        $reporte = $this->AutoLoadModel('reporte');
        $tipocambio = $this->AutoLoadModel('tipocambio');
        $Ordencompra = $reporte->reporteestructuracostos($anho, $mes);
        $tot1 = count($Ordencompra);

        $contenido = "";
        $TOTALcant = 0;
        $TOTALfob = 0;
        $TOTALcif = 0;
        $TOTALaduanas = 0;
        $TOTALalmacen = 0;
        $TOTALtransporte = 0;
        $TOTALgate_in = 0;
        $TOTALvobo = 0;
        $TOTALcadic1 = 0;
        $TOTALcadic2 = 0;
        $TOTALcadic3 = 0;
        $TOTALtotaldolares = 0;
        $PROMEDIOtc = 0;
        $TOTALtotalsoles = 0;
        $TOTALtotalunitario = 0;
        for($j=0;$j<$tot1;$j++){
                $contenido .= "<hr>";
                $contenido .= "<b>PERIODO:</b> ".$mes."/".$anho."<br>";
                $contenido .= "<b>DUA N°:</b> ".$Ordencompra[$j]['codigooc']."<br>";
                $contenido .= "<b>FECHA:</b> ".$Ordencompra[$j]['fordencompra']."<br>";
                $soles =  $tipocambio->consultatipocambioXfecha($Ordencompra[$j]['fordencompra']);
                $Detalleordencompra = $reporte->reporteDetalleCompraXMes($Ordencompra[$j]['idordencompra']);
                    $contenido .= "
                        <table>
                        <thead>
                        <tr>
                            <th>ITEM</th>
                            <th>Codigo</th>
                            <th>MERCADERIA</th>
                            <th>CANT</th>
                            <th>FOB</th>
                            <th>FLETE</th>
                            <th>SEGURO</th>
                            <th>CIF</th>
                            <th>AGC. ADUANAS<br>$</th>
                            <th>AGC. ALMACEN<br>$</th>
                            <th>AGC. TRANSPORTE<br>$</th>
                            <th>Gatein</th>
                            <th>V.B<br>$</th>
                            <th>Extra 1</th>
                            <th>Extra 2</th>
                            <th>Extra 3</th>
                            <th>TOTAL<br>$</th>
                            <th>T/C</th>
                            <th>TOTAL<br>S/.</th>
                            <th>TOTAL UNITARIO<br>S/.</th>
                        </tr>
                        </thead>
                        <tbody>";

                    $tot2 = count($Detalleordencompra);
                        for($i=0;$i<$tot2;$i++){
                            $cantidad=$Detalleordencompra[$i]['cantidadrecibidaoc'];
                            if($cantidad == 0) $contenido .= '<tr style="background: #FFB6C1">';
                            else $contenido .= '<tr>';

                            $contenido .= '<td><div style="float: right">'.($i+1).'</div></td>';
                            $contenido .= '<td>'.$Detalleordencompra[$i]['codigopa'].'</td>';
                            $contenido .= '<td>'.$Detalleordencompra[$i]['nompro'].'</td>';

                            $fob=$Detalleordencompra[$i]['fobdoc'];
                            $fobTotal=$fob*$cantidad;
                            $flete=!empty($Detalleordencompra[$i]['fleted'])?($Detalleordencompra[$i]['fleted']):"0.00";
                            $seguro=!empty($Detalleordencompra[$i]['seguro'])?($Detalleordencompra[$i]['seguro']):"0.00";
                            $ciftotal=$fobTotal+$seguro+$flete;

                            $contenido .= '<td><div style="float: right">'.$cantidad.'</div></td>';
                            $contenido .= '<td><div style="float: right">'.number_format($fobTotal,2).'</div></td>';
                            $contenido .= '<td><div style="float: right">'.$flete.'</div></td>';
                            $contenido .= '<td><div style="float: right">'.$seguro.'</div></td>';
                            $contenido .= '<td><div style="float: right">'.$ciftotal.'</div></td>';

                            $agenteaduanas=!empty($Detalleordencompra[$i]['agenteaduanas'])?($Detalleordencompra[$i]['agenteaduanas']):"0.00";
                            $contenido .= '<td><div style="float: right">'.$agenteaduanas.'</div></td>';

                            $flat=!empty($Detalleordencompra[$i]['flat'])?($Detalleordencompra[$i]['flat']):"0.00";
                            $contenido .= '<td><div style="float: right">'.$flat.'</div></td>';

                            $transporte=!empty($Detalleordencompra[$i]['fleteInterno'])?($Detalleordencompra[$i]['fleteInterno']):"0.00";
                            $contenido .= '<td><div style="float: right">'.$transporte.'</div></td>';

                            $gate_in=!empty($Detalleordencompra[$i]['gate_in'])?($Detalleordencompra[$i]['gate_in']):"0.00";
                            $contenido .= '<td><div style="float: right">'.$gate_in.'</div></td>';

                            $VoBo=!empty($Detalleordencompra[$i]['VoBo'])?($Detalleordencompra[$i]['VoBo']):"0.00";
                            $contenido .= '<td><div style="float: right">'.$VoBo.'</div></td>';

                            $cadic1=!empty($Detalleordencompra[$i]['cv1'])?($Detalleordencompra[$i]['cv1']):"0.00";
                            $cadic2=!empty($Detalleordencompra[$i]['cv2'])?($Detalleordencompra[$i]['cv2']):"0.00";
                            $cadic3=!empty($Detalleordencompra[$i]['cv3'])?($Detalleordencompra[$i]['cv3']):"0.00";

                            $contenido .= '<td><div style="float: right">'.$cadic1.'</div></td>';
                            $contenido .= '<td><div style="float: right">'.$cadic2.'</div></td>';
                            $contenido .= '<td><div style="float: right">'.$cadic3.'</div></td>';

                            $total = $ciftotal + $agenteaduanas + $flat + $transporte + $gate_in + $VoBo + $cadic1 + $cadic2 + + $cadic3;
                            $totalsoles = round(($soles*$total),2);
                            if($cantidad != 0)$totalunitario = $totalsoles/$cantidad;
                            else $totalunitario = 0;

                            $totalunitario = round($totalunitario,2);
                            $PROMEDIOtc = $PROMEDIOtc + $soles;
                            $contenido .= '<td><div style="float: right">'.$total.'</div></td>';
                            $contenido .= '<td><div style="float: right">'.$soles.'</div></td>';
                            $contenido .= '<td><div style="float: right">'.$totalsoles.'</div></td>';
                            $contenido .= '<td><div style="float: right">'.$totalunitario.'</div></td>';
                            $contenido .= '</tr><div style="float: right"></div></tr>';

                            $TOTALcant = $cantidad + $TOTALcant;
                            $TOTALfob = $fobTotal + $TOTALfob;

                            $TOTALseguro = $seguro + $TOTALseguro;
                            $TOTALcif = $ciftotal + $TOTALcif;
                            $TOTALaduanas = $agenteaduanas + $TOTALaduanas;
                            $TOTALalmacen = $flat + $TOTALalmacen;
                            $TOTALtransporte = $transporte + $TOTALtransporte;
                            $TOTALgate_in = $gate_in + $TOTALgate_in;
                            $TOTALvobo = $VoBo + $TOTALvobo;
                            $TOTALcadic1 = $cadic1 + $TOTALcadic1;
                            $TOTALcadic2 = $cadic2 + $TOTALcadic2;
                            $TOTALcadic3 = $cadic3 + $TOTALcadic3;
                            if($TOTALcadic1 == 0) $TOTALcadic1 = "0.00";
                            if($TOTALcadic2 == 0) $TOTALcadic2 = "0.00";
                            if($TOTALcadic3 == 0) $TOTALcadic3 = "0.00";
                            $TOTALtotaldolares = $total + $TOTALtotaldolares;
                            $TOTALtotalsoles = $totalsoles + $TOTALtotalsoles;
                            $TOTALtotalunitario = $totalunitario + $TOTALtotalunitario;
                        }
                        $contenido .= "<tfoot>";
                        $contenido .= "<tr>";
                            $contenido .= "<th colspan='3'><div style='float: right'>TOTAL:</div></th>";
                            $contenido .= "<th><div style='float: right'>".$TOTALcant."</div></th>";
                            $contenido .= "<th><div style='float: right'>".$TOTALfob."</div></th>";
                            $contenido .= "<th><div style='float: right'>".number_format($Ordencompra[$j]['flete'], 2)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".number_format($Ordencompra[$j]['seguro'], 2)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".number_format($Ordencompra[$j]['totalcif'], 2)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".number_format($Ordencompra[$j]['comisionagenteadu'], 2)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".number_format($Ordencompra[$j]['costoflat'], 2)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".number_format($Ordencompra[$j]['costofleteinterno'], 2)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".number_format($Ordencompra[$j]['costoalmacenvb'], 2)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".number_format($Ordencompra[$j]['costoalmacengate'], 2)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".number_format($Ordencompra[$j]['cv1'], 2)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".number_format($Ordencompra[$j]['cv2'], 2)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".number_format($Ordencompra[$j]['cv2'], 3)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".$TOTALtotaldolares."</div></th>";
                            $contenido .= "<th><div style='float: right'>".round(($PROMEDIOtc/$tot2),2)."</div></th>";
                            $contenido .= "<th><div style='float: right'>".$TOTALtotalsoles."</div></th>";
                            $contenido .= "<th><div style='float: right'>".$TOTALtotalunitario."</div></th>";
                        $contenido .= "</tr>";
                        $contenido .= "</tfoot>";
                            $TOTALcant = 0;
                            $TOTALfob = 0;
                            $TOTALflete = 0;
                            $TOTALseguro = 0;
                            $TOTALcif = 0;
                            $TOTALaduanas = 0;
                            $TOTALalmacen = 0;
                            $TOTALtransporte = 0;
                            $TOTALgate_in = 0;
                            $TOTALvobo = 0;
                            $TOTALcadic1 = 0;
                            $TOTALcadic2 = 0;
                            $TOTALcadic3 = 0;
                            $PROMEDIOtc = 0;
                            $TOTALtotaldolares = 0;
                            $TOTALtotalsoles = 0;
                            $TOTALtotalunitario = 0;
                    $contenido .= "</tbody></table>";
                    $contenido .= "<hr><br><br>";
        }

        echo $contenido;
    }

    function reportevendedores() {
        $opcion = $_REQUEST['cbopcion'];
        $Mvendedor = $this->AutoLoadModel('actor');
        $vendedor = $Mvendedor->listaVendedores($opcion);

        $TVendedor=count($vendedor);
        $contenido.= "<table>
                        <thead>
                            <tr>
                                <th>
                                    ID
                                </th>
                                <th>
                                    NOMBRE COMPLETO
                                </th>
                                <th>
                                    DIRECCION
                                </th>
                                <th>
                                    TELEFONO
                                </th>
                                <th>
                                    CELULAR
                                </th>
                                <th>
                                    RPM
                                </th>
                                <th>
                                    CODIGO VENDEDOR
                                </th>
                                <th>
                                    E-MAIL
                                </th>
                                <th>
                                    DNI
                                </th>
                            </tr>
                        </thead>
                        <tbody>";

        for($i=0;$i<$TVendedor;$i++){
            if ($vendedor[$i]['estado'] == 0) {
                $claseinactivo = 'style="background:#FED4D4;"';
            } else {
                $claseinactivo = "";
            }
            $contenido.="<tr ".$claseinactivo.">";
		$contenido.="<td>".STRTOUPPER($vendedor[$i]['idactor'])."</td>";
		$contenido.="<td>".$vendedor[$i]['nombres']." ".$vendedor[$i]['apellidopaterno']." ".$vendedor[$i]['apellidomaterno']."</td>";
		$contenido.="<td>".$vendedor[$i]['direccion']."</td>";
		$contenido.="<td>".$vendedor[$i]['telefono']."</td>";
		$contenido.="<td>".$vendedor[$i]['celular']."</td>";
		$contenido.="<td>".$vendedor[$i]['rpm']."</td>";
		$contenido.="<td>".$vendedor[$i]['codigoa']."</td>";
		$contenido.="<td>".$vendedor[$i]['email']."</td>";
		$contenido.="<td>".$vendedor[$i]['dni']."</td>";
            $contenido.="</tr>";
        }

        $contenido.= "</tbody>
                     </table>";


        echo $contenido;

    }

    function resumendekardex() {
        $mes = $_REQUEST['INICIOopcmes'];
        $anho = $_REQUEST['INICIOopcanho'];

        $FINmes = $_REQUEST['FINopcmes'];
        $FINanho = $_REQUEST['FINopcanho'];

        $txtproducto = $_REQUEST['txtproducto'];
        $PROD = $this->AutoLoadModel('producto');
        $movimiento = new Movimiento();

        if (!empty($txtproducto)) {
            $totalProductos = 1;
        } else {
            $listaProductos = $PROD->listadoProductos();
            $totalProductos = count($listaProductos);

        }
        $contenido.= "<table>
                        <thead>
                            <tr>
                                <th rowspan='2'>
                                    CODIGO
                                </th>
                                <th rowspan='2'>
                                    PRODUCTO
                                </th>
                                <th colspan='2'>
                                    SALDO INICIAL
                                </th>
                                <th colspan='2'>
                                    COMPRAS
                                </th>
                                <th colspan='2'>
                                    TOTAL
                                </th>
                                <th colspan='2'>
                                    EXISTENCIA FINAL
                                </th>
                                <th colspan='2'>
                                    STOCK ACTUAL
                                </th>
                            </tr>
                            <tr>
                                <th>CANTIDAD</th>
                                <th>SOLES (S/.)</th>

                                <th>CANTIDAD</th>
                                <th>SOLES (S/.)</th>

                                <th>CANTIDAD</th>
                                <th>SOLES (S/.)</th>

                                <th>CANTIDAD</th>
                                <th>SOLES (S/.)</th>

                                <th>CANTIDAD</th>
                                <th>SOLES (S/.)</th>
                            </tr>
                        </thead>
                        <tbody>";
        for ($contador = 0; $contador < $totalProductos; $contador++) {

            if (!empty($txtproducto)) {
                $codigoProductobuscar = $txtproducto;
                $producto = $PROD->buscaProducto($codigoProductobuscar);
                $nombreProducto = $producto[0]['nompro'];
                $codigoProducto = $producto[0]['codigopa'];
            } else {
                $codigoProductobuscar = $listaProductos[$contador]['idproducto'];
                $nombreProducto = $listaProductos[$contador]['nompro'];
                $codigoProducto = $listaProductos[$contador]['codigopa'];
            }

            $dataKardex = $movimiento->resumenKardexXProducto($codigoProductobuscar, $anho, $FINanho, $mes, $FINmes);
            $total = count($dataKardex);

            if ($dataKardex[0]['tipomovimiento'] == 1) {
                $cantidadINICIAL = round($dataKardex[0]['SaldoCantidad'] - round($dataKardex[0]['cantidad']));
                if ($cantidadINICIAL < 0) {
                    $cantidadINICIAL = 0;
                }
            } else {
                $cantidadINICIAL = round($dataKardex[0]['SaldoCantidad'] + round($dataKardex[0]['cantidad']));
            }

            $costoINICIAL = round($dataKardex[0]['SaldoPrecio'] * $cantidadINICIAL, 2);

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
            }

            for ($i = 0; $i < $total; $i++) {
                $cantidadCOMPRAS = $cantidadCOMPRAS + $dataKardex[$i]['EntradaCantidad'];
                $costoCOMPRAS = $costoCOMPRAS + (empty($dataKardex[$i]['EntradaCosto']) ? '' : number_format($dataKardex[$i]['EntradaPrecio'], 2));

                $solesEXISTENCIA = $solesEXISTENCIA + (empty($dataKardex[$i]['SalidaCosto']) ? '' : number_format($dataKardex[$i]['SalidaPrecio'], 2));
                $cantidadEXISTENCIA = $cantidadEXISTENCIA + $dataKardex[$i]['SalidaCantidad'];

                $tecant+=$dataKardex[$i]['EntradaCantidad'];
                $tecosto+=$dataKardex[$i]['EntradaCosto'];
                $tscant+=$dataKardex[$i]['SalidaCantidad'];
                $tscosto+=$dataKardex[$i]['SalidaCosto'];
            }

            $contenido .= "<tr>
                                <td>" . $codigoProducto . "</td>
                                <td>" . $nombreProducto . "</td>
                                <td>" . $cantidadINICIAL . "</td>
                                <td>S/. ";
            if ($costoINICIAL == 0)
                $contenido.= "0.00";
            $contenido.= "" . $costoINICIAL;
            $contenido.= "</td>
                                <td>" . round($tecant) . "</td>
                                <td>S/. " . number_format($tecosto, 2) . "</td>
                                <td>" . ($cantidadINICIAL + round($tecant)) . "</td>
                                <td>S/. " . number_format(($costoINICIAL + $tecosto), 2) . "</td>
                                <td>" . round($tscant) . "</td>
                                <td>S/. " . number_format($tscosto, 2) . "</td>
                                <td><b>" . (round(($cantidadINICIAL + $tecant) - $tscant)) . "</b></td>
                                <td><b>S/. " . (number_format((($costoINICIAL + $tecosto) - $tscosto), 2)) . "</b></td>
                            </tr>";
        }
        $contenido .= "</tbody>
                      </table>";

        echo $contenido;
    }

    function reporteUtilidadesComision() {
        $this->view->show('/reporte/reporteUtilidadesComision.phtml', $data);
    }

    function reporteFacturacion() {
        $this->view->show('/reporte/reporteFacturacion.phtml', $data);
    }

    function reporteKardexProduccion() {
        $this->view->show('/reporte/reporteKardexProduccion.phtml', $data);
    }

    function reporteKardexProduccionRepuesto() {
        $this->view->show('/reporte/reporteKardexProduccionRepuesto.phtml', $data);
    }

    function letrasxhacerplanilla() {
        $this->AutoLoadModel('reporte');
        $this->view->show('/reporte/letrasxhacerplanilla.phtml');
    }

    function reporteCarteraCobranza() {
        $linprod = $_REQUEST['lstLineaProductos'];
        $idvend = $_REQUEST['idVendedor'];
        $condicion = $_REQUEST['lstCondicion'];
        $catprin = $_REQUEST['lstCategoriaPrincipal'];
        $regcobr = $_REQUEST['lstRegionCobranza'];
        $zona = $_REQUEST['lstZona'];
        $fecini = $_REQUEST['txtFechaInicio'];
        $fecfin = $_REQUEST['txtFechaFin'];
        $depa = $_REQUEST['lstDepartamento'];
        $prov = $_REQUEST['lstProvincia'];
        $dist = $_REQUEST['lstDistrito'];
        $ordenar = $_REQUEST['lstOrden'];
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        $cartcli = new CarteraCliente($idvend, $condicion, $catprin, $regcobr, $zona, $fecini, $fecfin, $depa, $prov, $dist, $ordenar);
        $valores = $cartcli->listarCarteraPaginado($pagina);
        $paginacion = $cartcli->paginadoCartera();

        $retornar = "<thead>"
                . "<tr>"
                . "<th>CLIENTE</th>"
                . "<th>RUC</th>"
                . "<th>TELEFONO</th>"
                . "<th>DIRECCION</th>"
                . "<th>LINEAS</th>"
                . "<th>PROMEDIO COMPRAS</th>"
                . "<th>CALIFICACION</th>"
                . "</tr>"
                . "</thead>"
                . "<tbody>";
        $total = count($valores);
        $zona = "";
        $linea = new Linea();
        for ($i = 0; $i < $total; $i++) {
            /*$reg = $linea->lineasCliente($datos[$i]['idcliente']);
            $j = count($reg);
            $ejecuta = FALSE;
            if (empty($linprod)) {
                $ejecuta = TRUE;
            }
            else {
                for($k = 0; $k<$j; $k++) {
                    if ($reg[$k]['idlinea'] == $linprod) {
                        $ejecuta = TRUE;
                        break;
                    }
                }
            }

            $lineas = "";
            for($k = 0; $k<$j; $k++) {
                if ($k!=0) {
                    $lineas .= ", ";
                }
                $lineas .= $reg[$k]['nomlin'];
            }

            if ($ejecuta) {*/
                if ($zona != $valores[$i]['nombrezona']) {
                    $retornar .= "<tr><td colspan=\"8\"><b>Zona: " . $valores[$i]['nombrezona'] . "</b></td></tr>";
                    $zona = $valores[$i]['nombrezona'];
                }
                $retornar .= "<td style=\"text-align: left;\">" . $valores[$i]['cliente'] . "</td>"
                        . "<td style=\"text-align: center;\">" . $valores[$i]['ruc'] . "</td>"
                        . "<td style=\"text-align: center;\">" . $valores[$i]['telefono'] . (empty($valores[$i]['telefono']) || empty($valores[$i]['celular']) ? "" : " / ") . $valores[$i]['celular'] . "</td>"
                        . "<td style=\"text-align: left;\">" . $valores[$i]['direccion'] . ", " . $valores[$i]['dist'] . " - " . $valores[$i]['prov'] . " - " . $valores[$i]['depa'] . "</td>"
                        . "<td style=\"text-align: center;\">" . $lineas . "</td>"
                        . "<td style=\"text-align: right;\">" . round($datos[$i]['sumtotal'], 2) . "</td>"
                        . "<td style=\"text-align: center;\">" . ($valores[$i]['diasmora']<=12 ? 'BUENO' : ($valores[$i]['diasmora']>12&&$valores[$i]['diasmora']<=90 ? 'REGULAR' : 'CLASE CM'))." (".$datos[$i]['diasmora'].")" . "</td>"
                        . "</tr>";
            /*}
            else {
                $paginacion--;
            }*/
        }
        $retornar .= "</tbody>";

        $blockpaginas = round($paginacion / 10);
        if ($blockpaginas * 10 < $paginacion) {
            $blockpaginas = $blockpaginas + 1;
        }

        $retornar .="<tfoot><tr><td align='center' colspan=\"12\">";
        if ($pagina > 1) {
            $retornar .= "<a data-pag=\"" . ($pagina - 1) . "\" href=\"#\" id=\"pag-" . ($pagina - 1) . "\"> " . "Anterior" . " </a>";
        }

        for ($i = 1; $i <= $blockpaginas; $i++) {
            $max = $i * 10;

            for ($min = $max - 9; $min <= $max; $min++) {
                if ($pagina >= $max - 9 && $pagina <= $max && $paginacion >= $min) {
                    if ($pagina == $min) {
                        $retornar .= "<a data-pag=\"" . ($min) . "\" href=\"#\" id=\"pag-" . ($min) . "\"> <b style='color:blue;'>" . ($min) . " </b></a>";
                    } else {
                        $retornar .= "<a data-pag=\"" . ($min) . "\" href=\"#\" id=\"pag-" . ($min) . "\"> " . ($min) . " </a>";
                    }
                }
            }
        }

        if ($pagina < $paginacion && !empty($pagina)) {
            $retornar .= "<a data-pag=\"" . ($pagina + 1) . "\" href=\"#\" id=\"pag-" . ($pagina + 1) . "\"> " . "Siguiente" . " </a>";
        }

        $retornar .="</td></tr></tfoot>";
        $retornar .="<script>"
                . "$(document).ready(function(){"
                . "$('[id^=pag]').click(function(e){
                                e.preventDefault();
                                $('#carteraCliente').html('');
                                $('#carteraCliente').html('<th style=\"text-align: center;\"><img style=\"width:250px;heigth:100\" src=\"/imagenes/cargando.gif\"></th>');
                                cargaConsulta($(this).data('pag'));
                                });
                                $(\"[id^=dialog]\").dialog({
                                autoOpen: false,
                                show: {
                                effect: \"blind\",
                                duration: 500
                                },
                                hide: {
                                effect: \"blind\",
                                duration: 500
                                },
                                buttons: {
                                Cerrar: function() {
                                $(this).dialog(\"close\");
                                }
                                }
                                });
                                $(\"[id^=opener]\").click(function() {
                                $(\"#dialog-\"+$(this).data('num')).dialog(\"open\");
                                });"
                . "});"
                . "</script>";
        echo $retornar;
    }

    function ventasxmes() {
        $data['documentos'] = $this->tipoDocumento();
        $this->view->show('/reporte/ventasxmes.phtml', $data);
    }

    function guiasydocumentos() {
        $this->view->show('/reporte/guiasydocumentos.phtml');
    }

    function estadoproductos() {
        $this->view->show('/reporte/estadoproductos.phtml');
    }

    function resumencobranzas() {
        $zona = $this->AutoLoadModel('zona');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['hijo'] = $zona->listacategoriaHijo();
        $data['zona'] = $zona->listadoTotalZona();
        $this->view->show('/reporte/resumencobranzas.phtml',$data);
    }
    function durezaproducto() {
        $linea = new Linea();
        $data['Linea'] = $linea->listadoLineas('idpadre=0');
        $this->view->show('/reporte/durezaproductos.phtml', $data);
    }

    function reportezeta() {
        $this->view->show('/reporte/reporteDetalleOrdenCompra.phtml');
    }

    function seguimientocobranza() {
        $this->view->show('/reporte/seguimientocobranza.phtml');
    }

    function rankingvendedor() {
        $this->view->show('/reporte/rankingvendedor.phtml');
    }

    function cantidadventasxmes() {
        if (!empty($_REQUEST['txtidLinea']) || !empty($_REQUEST['txtidSubLinea']) || !empty($_REQUEST['txtidproducto'])) {
            $idLinea = $_REQUEST['txtidLinea'];
            $idSubLinea = $_REQUEST['txtidSubLinea'];
            $idProducto = $_REQUEST['txtidproducto'];
            $mes = array(1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE');
            $repote = new Reporte();
            $ordencompra = new Ordencompra();
            $ordenventa = new OrdenVenta();
            $productos = $repote->reporteIdproductos($idLinea, $idSubLinea, $idProducto);
            for ($p = 0; $p < count($productos); $p++) {
                $oc = $ordencompra->reporteOrdenCompraProducto($productos[$p]['idproducto']);

                if (count($oc) == 1) {
                    echo '<fieldset>';
                    $ov = $ordenventa->ultimaOrdenVentaxProducto($productos[$p]['idproducto']);
                    echo '<table>
                            <thead>
                                <tr>
                                    <th colspan="8" style="background:#B4D1F7;color:#830E0E;"><h1><b>' . $oc[0]['nompro'] . '</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th style="color:black;background:#C6DCF9;"><b>Código: </b></th><td>' . $oc[0]['codigopa'] . '</td>
                                    <th style="color:black;background:#C6DCF9;"><b>Linea: </b></th><td>' . $oc[0]['linea'] . '</td>
                                        <th style="color:black;background:#C6DCF9;"><b>Sub-Linea: </b></th><td>' . $oc[0]['sublinea'] . '</td>
                                    <th style="color:black;background:#C6DCF9;"><b>Unidad de Medida: </b></th><td>' . $oc[0]['nommedida'] . '</td>
                                </tr>
                                <tr>
                                    <th colspan="4" style="background:#B4D1F7;color:#830E0E;"><h1><b>INGRESO DEL PRODUCTO</b></th>
                                    <th colspan="4" style="background:#B4D1F7;color:#830E0E;"><h1><b>ULTIMA SALIDA DEL PRODUCTO</b></th>
                                </tr>';
                    if (count($ov) == 1) {
                        echo '            <tr>
                                    <th style="color:black;background:#C6DCF9;"><b>Orden de Compra: </b></th><td>' . $oc[0]['codigooc'] . '</td>
                                    <th style="color:black;background:#C6DCF9;"><b>Fecha de Llegada: </b></th><td>' . $oc[0]['fecha'] . '</td>
                                    <th style="color:black;background:#C6DCF9;"><b>Orden de Venta: </b></th><td>' . $ov[0]['codigov'] . '</td>
                                    <th style="color:black;background:#C6DCF9;"><b>Fecha de Salida: </b></th><td>' . $ov[0]['fordenventa'] . '</td>
                                </tr>
                            </tbody>
                        </table>';
                        $canOv = $ordenventa->ListarCantidadVendida($productos[$p]['idproducto'], $oc[0]['fecha'], $ov[0]['fordenventa']);
                        echo '<table>
                            <thead>
                                <tr>
                                    <th colspan="2" style="background:#B4D1F7;color:#830E0E;"><h1><b>Cantidad de Ventas Por Mes</b></th>
                                </tr>
                            </thead>
                            <tbody>';
                        echo '<tr>';
                        echo '<th style="color:black;background:#C6DCF9;"><b>Mes</b></th>';
                        echo '<th style="color:black;background:#C6DCF9;"><b>Cantidad Vendida</b></th>';
                        echo '</tr>';
                        $total = 0;
                        $auxMes = explode("-", $oc[0]['fecha']);
                        $mesActual = $auxMes[1];
                        $cantidad = 0;
                        for ($i = 0; $i < count($canOv); $i++) {
                            $auxMes = explode("-", $canOv[$i]['fordenventa']);
                            if ($mesActual == $auxMes[1]) {
                                $cantidad = $cantidad + $canOv[$i]['cantidad'];
                            } else {
                                $total = $total + $cantidad;
                                echo '<tr>';
                                echo '<td><center>' . $mes[$mesActual * 1] . '</center></td>';
                                echo '<td><center>' . $cantidad . '</center></td>';
                                echo '</tr>';
                                $cantidad = 0;
                                if ($mesActual != 12)
                                    $mesActual++;
                                else
                                    $mesActual = 1;
                                $i = $i - 1;
                            }
                        }
                        if ($i > 1) {
                            $total = $total + $cantidad;
                            echo '<tr>';
                            echo '<td><center>' . $mes[$mesActual * 1] . '</center></td>';
                            echo '<td><center>' . $cantidad . '</center></td>';
                            echo '</tr>';
                        }
                        echo '  </tbody>
                            <tfoot>
                                <tr>
                                    <th style="color:black;background:#C6DCF9;"><b>Cantidad Total</b></th>
                                    <td><center>' . $total . '</center></td>
                                </tr>
                            </tfoot>
                          </table>';
                    } else {
                        echo '            <tr>
                                    <th style="color:black;background:#C6DCF9;"><b>Orden de Compra: </b></th><td>' . $oc[0]['codigooc'] . '</td>
                                    <th style="color:black;background:#C6DCF9;"><b>Fecha de Llegada: </b></th><td>' . $oc[0]['fecha'] . '</td>
                                    <th style="color:black;background:#C6DCF9;"><b>Orden de Venta: </b></th><td> --- </td>
                                    <th style="color:black;background:#C6DCF9;"><b>Fecha de Salida: </b></th><td> --- </td>
                                </tr>
                            </tbody>
                        </table>';
                        echo '<table>
                            <thead>
                                <tr>
                                    <th colspan="2" style="background:#B4D1F7;color:#830E0E;"><h1><b>Cantidad de Ventas Por Mes</b></th>
                                </tr>
                            </thead>
                            <tbody>';
                        echo '<tr>';
                        echo '<th style="color:black;background:#C6DCF9;"><b>Mes</b></th>';
                        echo '<th style="color:black;background:#C6DCF9;"><b>Cantidad Vendida</b></th>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<th style="color:black;background:#C6DCF9;" colspan="2"><b>ESTE PRODUCTO AUN NO HA SIDO VENDIDO</b></th>';
                        echo '</tr>';
                        echo '</tbody>
                          </table>';
                    }
                    echo '</fieldset>';
                } else {
                    if (!empty($_REQUEST['txtidproducto'])) echo "<fieldset><b>NO SE DETECTO ORDEN DE COMPRA</b></fieldset>";
                }
            }
        } else {
            $linea = new Linea();
            $data['Linea'] = $linea->listadoLineas('idpadre=0');
            $this->view->show('/reporte/Ventasxmesxproducto.phtml', $data);
        }
    }

    function prestamospersonal() {
        $detalle = $this->AutoLoadModel('DetalleOrdenVenta');
        $ordenGasto = $this->AutoLoadModel('ordengasto');

        $ordenes = $detalle->importesProductoDeuda();
        $tam = count($ordenes);
        $nro = 1;
        $contenido = "";
        $cliente = -1;
        $Sdeuda = 0;
        $Ddeuda = 0;
        set_time_limit(500);
        for ($i = 0; $i < $tam; $i++) {
            if ($cliente == $ordenes[$i]['idcliente']) {
                if ($ordenes[$i]['simbolo'] == 'S/') $Sdeuda = ($ordenGasto->totalGuia($ordenes[$i]['idordenventa']) - $ordenes[$i]['importepagado']) + $Sdeuda;
                else $Ddeuda = ($ordenGasto->totalGuia($ordenes[$i]['idordenventa']) - $ordenes[$i]['importepagado']) + $Ddeuda;
            } else {
                if ($Sdeuda > 0 || $Ddeuda > 0) {
                    $contenido .= '<tr>';
                        $contenido .= '<td>'.$nro.'</td>';
                        $contenido .= '<td>'.$ordenes[$i-1]['razonsocial']."</td>";
                        $contenido .= '<td>S/. '.number_format($Sdeuda, 2).'</td>';
                        $contenido .= '<td>US $. '.number_format($Ddeuda, 2).'</td>';
                    $contenido .= '</tr>';
                    $nro ++;
                }
                $cliente = $ordenes[$i]['idcliente'];
                if ($ordenes[$i]['simbolo'] == 'S/') {
                    $Sdeuda = ($ordenGasto->totalGuia($ordenes[$i]['idordenventa']) - $ordenes[$i]['importepagado']);
                    $Ddeuda = 0;
                } else {
                    $Ddeuda = ($ordenGasto->totalGuia($ordenes[$i]['idordenventa']) - $ordenes[$i]['importepagado']);
                    $Sdeuda = 0;
                }
            }
        }

        if ($Sdeuda > 0 || $Ddeuda > 0) {
            $contenido .= '<tr>';
                $contenido .= '<td>'.$nro.'</td>';
                $contenido .= '<td>'.$ordenes[$i-1]['razonsocial']."</td>";
                $contenido .= '<td>S/. '.number_format($Sdeuda, 2).'</td>';
                $contenido .= '<td>US $. '.number_format($Ddeuda, 2).'</td>';
            $contenido .= '</tr>';
        }
        $data['contenido'] = $contenido;
        $this->view->show('/reporte/prestamopersonal.phtml', $data);
    }

    function listaGerencial() {
        if (count($_REQUEST) == 6) {
            $linea = new Linea();
            $almacen = new Almacen();
            $opciones = new general();
            $url = "/" . $_REQUEST['url'];
            $data['Opcion'] = $opciones->buscaOpcionexurl($url);
            $data['Modulo'] = $opciones->buscaModulosxurl($url);
            $data['Linea'] = $linea->listadoLineas('idpadre=0');
            $data['Almacen'] = $almacen->listadoAlmacen();
            $this->view->show('reporte/listagerencial.phtml', $data);
        } else {
            $idAlmacen = $_REQUEST['idAlmacen'];
            $idLinea = $_REQUEST['idLinea'];
            $idSubLinea = $_REQUEST['idSubLinea'];
            $idProducto = $_REQUEST['idProducto'];
            $moneda = $_REQUEST['opcmoneda'];
            $lstStock = $_REQUEST['tipoStock'];
            $reporte = new Reporte();
            $data = $reporte->reporteListaPrecio_consinstock($idAlmacen, $idLinea, $idSubLinea, $idProducto, $lstStock);
            $rutaImagen = $this->rutaImagenesProducto();
            $unidadMedida = $this->unidadMedida();
            $data2 = array();
            for ($i = 0; $i < count($data); $i++) {
                //      echo '<td><img src="'.$rutaImagen.$data[$i]['codigo'].'/'.$data[$i]['imagen'].'" width="50" height="50"></td>';
                $data2[$i]['codigo'] = $data[$i]['codigopa'];
                $data2[$i]['nompro'] = $data[$i]['nompro'];
                $data2[$i]['stockactual'] = $data[$i]['stockactual'];

                if ($moneda == 1) {
                    $data2[$i]['preciolista'] = $data[$i]['preciolista'];
                }
                if ($moneda == 2) {
                    $data2[$i]['preciolista'] = $data[$i]['preciolistadolares'];
                }
                $dataordencompra = $reporte->getUltimaOrdenCompra($data[$i]['idproducto']);
                $data2[$i]['fuc'] = $dataordencompra[0]['fordencompra'];
                $data2[$i]['cant'] = $dataordencompra[0]['cantidadrecibidaoc'];
                $data2[$i]['cif'] = (empty($data[$i]['cifventasdolares']) ? '0.00' : $data[$i]['cifventasdolares']);
                $data2[$i]['fob'] = (empty($data[$i]['fob']) ? '0.00' : $data[$i]['fob']);
                $data2[$i]['precioneto'] = number_format($data2[$i]['preciolista'] - ($data2[$i]['preciolista'] * 0.289417), 2);
                $data2[$i]['utilidad'] = $data2[$i]['cif'] == 0.00 ? 'CIF = 0.00' : (empty($data[$i]['cifventasdolares']) ? 'CIF no asignado' : number_format(((($data2[$i]['precioneto'] - $data2[$i]['cif']) / $data2[$i]['cif']) * 100), 2)) . ' %';
            }
            if (empty($data2)) {
                $data2[0]['codigo'] = '--';
                $data2[0]['nompro'] = '--';
                $data2[0]['stockactual'] = '--';
                $data2[0]['preciolista'] = '--';
                $data2[0]['fuc'] = '--';
                $data2[0]['cant'] = '--';
                $data2[0]['cif'] = '--';
                $data2[0]['fob'] = '--';
                $data2[0]['precioneto'] = '--';
                $data2[0]['utilidad'] = '--';
            }
            $objeto = $this->formatearparakui($data2);
            header("Content-type: application/json");
            //echo "{\"data\":" .json_encode($objeto). "}";
            echo json_encode($objeto);
        }
    }

    function costodeproductos() {
        $this->view->show('/reporte/costodeproductos.phtml');
    }

    function costodeproductos_mostrar () {
        $ordencompra = new Ordencompra();
        $datos = $ordencompra->reporteCostodeProducto();
        $tam = count($datos);
        echo "<table>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th colspan='4' style='background:#B4D1F7;color:#830E0E;'>REPORTE - COSTO DE PRODUCTOS</th>";
                echo "<tr>";
                echo "<tr>";
                    echo "<th colspan='2'>FECHA DE CONSULTA:</th>";
                    echo "<td colspan='2'>" . date("Y-m-d")  . "</td>";
                echo "<tr>";
            echo "</thead>";
            echo "<tbody>";
                echo "<tr>";
                    echo "<th>ITEM</th>";
                    echo "<th>CODIGO</th>";
                    echo "<th>DESCRIPCION</th>";
                    echo "<th>PRECIO UNITARIO (U$.)</th>";
                echo "<tr>";
            $idproducto = 0;
            $cant = 1;
            for ($i = 0; $i < $tam; $i++) {
                if ($idproducto != $datos[$i]['idproducto']) {
                    echo "<tr>";
                        echo "<td>" . $cant . "</td>";
                        echo "<td>" . $datos[$i]['codigopa'] . "</td>";
                        echo "<td>" . $datos[$i]['nompro']. "</td>";
                        echo "<td>U$. " . number_format($datos[$i]['totalunitario'], 2) . "</td>";
                    echo "<tr>";
                    $cant ++;
                    $idproducto = $datos[$i]['idproducto'];
                }

            }
            echo "</tbody>";
        echo "</table>";

    }

    function reporteVentasCliente() {
        $this->view->show('/reporte/reporteVentasCliente.phtml');
    }


    function reporteConsultaPedidoVentas1() {

            $idvendedor = empty($_REQUEST['idVendedor'])? '':$_REQUEST['idVendedor'];
            $fechaInicial = empty($_REQUEST['txtFechaInicio'])? '':$_REQUEST['txtFechaInicio'];
            $fechaFinal = empty($_REQUEST['txtFechaFin'])? '':$_REQUEST['txtFechaFin'];
            $provinciax = empty($_REQUEST['lstProvinciax'])? '':$_REQUEST['lstProvinciax'];
            $reporte = new Reporte();
    $data = $reporte->reportePedidoVentas1($idvendedor,$fechaInicial,$fechaFinal,$provinciax);

            $cantidad = count($data);

            echo "<table>";
            echo "<thead>";
                echo "<tr style='background-color:#0693DE;color:white;'>";
                echo "<td>#</td>";
                echo "<td>DISTRITO</td>";
                echo "<td># CLIENTE</td>";
                echo "<td>VENTA TOTAL</td>";
                echo "<td>PROM. VENTAS</td>";
                echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            $contClientes = 0;
            $sumaVenta = 0;
            $contador = 0;
            $distritoAnt = 0;


            for($i = 0; $i <= $cantidad; $i++){

                if($data[$i]['IDDISTRITO'] == $distritoAnt || $i == 0){
                    $contClientes++;
                    $distrito = $data[$i]['DISTRITO'];
                    $sumaVenta += $data[$i]['VENTAS'];
                    $distritoAnt = $data[$i]['IDDISTRITO'];
                }else{

                    $contador++;
                    echo "<tr>";
                    echo "<td>".$contador."</td>";
                    echo "<td>".$distrito."</td>";
                    echo "<td>".$contClientes."</td>";
                    echo "<td>".$sumaVenta."</td>";
                    echo "<td>".round($sumaVenta/$contClientes,2)."</td>";
                    echo "</tr>";


                    $contClientes = 1;
                    $sumaVenta = $data[$i]['VENTAS'];
                    $distritoAnt = $data[$i]['IDDISTRITO'];
                    $distrito = $data[$i]['DISTRITO'];
                }

            }


            echo "</tbody>";
            echo "</table>";

    }



    /* INICIO PORCENTAJE */
    function reportePorcentajeAnualZona() {
        $zona = $this->AutoLoadModel('zona');
        $tipo = $this->AutoLoadModel('tipocobranza');
        $empresa = $this->AutoLoadModel('almacen');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['tipocobranza'] = $tipo->lista();
        $data['empresa'] = $empresa->listado();

        $producto=$this->AutoLoadModel("Producto");
        $dataProducto=$producto->ValorizadoxLinea();
        $this->AutoLoadLib(array('GoogChart','GoogChart.class'));
        $data['datos']=$dataProducto;
        $data['grafico']=new GoogChart();
        $this->view->show("/reporte/reporteVenta1.phtml", $data);
    }
    function reportePorcentajej(){
        //
        $zonaGeografica = !empty($_REQUEST['lstCategoriaPrincipal'])?$_REQUEST['lstCategoriaPrincipal']:"";
        $idCategoria = !empty($_REQUEST['lstZonaCobranza'])?$_REQUEST['lstZonaCobranza']:"";
        $fechaInicio = !empty($_REQUEST['txtFechaInicio'])?$_REQUEST['txtFechaInicio']:"";
        $fechaFin = !empty($_REQUEST['txtFechaFinal'])?$_REQUEST['txtFechaFinal']:"";
        //$idAnio = !empty($_REQUEST['lstAnio'])?$_REQUEST['lstAnio']:"";
        $idVendedor = !empty($_REQUEST['idVendedor'])?$_REQUEST['idVendedor']:"";
        $idZona = !empty($_REQUEST['lstZona'])?$_REQUEST['lstZona']:"";

        $filtro = "ov.estado = 1 ";
        if(!empty($idVendedor)){
            $filtro.= " and a.idactor = ".$idVendedor;
        }
        if(!empty($fechaInicio)){
            $datei = explode('/',$fechaInicio);
            $fechai = $datei[0]."-".$datei[1]."-".$datei[2];
            $filtro .= " and ov.fordenventa >= '".$fechai."' ";
        }
        if(!empty($fechaFin)){
            $datef = explode('/',$fechaFin);
            $fechaf = $datef[0]."-".$datef[1]."-".$datef[2];
            $filtro .= " and ov.fordenventa <= '".$fechaf."' ";
        }

        if(empty($idZona)){

            $reporte = new Reporte();
            $zona = new Zona();
            $data = $reporte->obtenerVentaxZonaProcentaje($idCategoria,$filtro);
            $nombreGeografica = $zona->getnombreZona($zonaGeografica);
            $nombreCategoria = $zona->getnombreZona($idCategoria);

            if(count($data) != 0){

                $cantidad = count($data);
                $anterior = "zzz";
                $cont=0;
                $suma[$cont] = 0;
                for($i = 0; $i < $cantidad; $i++){
                    if($data[$i]['zona'] == $anterior || $i == 0){
                        $anterior = $data[$i]['zona'];
                        $nombre[$cont] = $data[$i]['zona'];
                        $suma[$cont] += $data[$i]['monto'];

                    }else{
                        $anterior = $data[$i]['zona'];
                        $cont++;
                        $suma[$cont];
                        $nombre[$cont] = $data[$i]['zona'];
                        $suma[$cont] += $data[$i]['monto'];
                    }
                }

                $sumaTotal = 0;
                for($i=0;$i<=$cont;$i++){
                    $sumaTotal += $suma[$i];
                }

                for($i=0;$i<=$cont;$i++){
                    $dato[$i]['porc'] = 100*($suma[$i]/$sumaTotal);
                    $dato[$i]['zona'] = $nombre[$i];
                    $dato[$i]['monto'] = $suma[$i];
                $graf[$nombre[$i]." = ".$suma[$i]."(".round($dato[$i]['porc'],2)."%)"] = $dato[$i]['porc'];
                }

            }

            echo "<h2>VENTAS POR ZONA: <b> ".$nombreGeografica[0]['nombrec']." >>> ".$nombreCategoria[0]['nombrec']."</b></h2>";

            if(count($data) != 0){
                $color = array( '#ccff00', '#7498e9', '#000faa',);
                $date=date('\a \l\a\s g:i a \d\e\l d.m.Y ');
                $this->AutoLoadLib(array('GoogChart','GoogChart.class'));
                $grafico=new GoogChart();
                $grafico->setChartAttrs(
                    array   (
                                'type' => 'pie',
                                'title' => $date,
                                'data' => $graf,
                                'size' => array(800,300),
                                'color' => $color
                            )
                );
                echo $grafico;
            }else{
                echo "<h3>NO SE HA ENCONTRADO NINGUNA ORDEN DE VENTA</h3>";
            }
        }else{
            // QUE PASÓ JIMMY
            $reporte = new Reporte();
            $zona = new Zona();
            $data = $reporte->obtenerVentaxZonaProcentaje2($idCategoria,$filtro);
            $nombreGeografica = $zona->getnombreZona($zonaGeografica);
            $nombreCategoria = $zona->getnombreZona($idCategoria);
            $nombreCategoria = $zona->getnombreZona2($idZona);

            if(count($data) != 0){

                $cantidad = count($data);
                $anterior = "zzz";
                $cont=0;
                $suma[$cont] = 0;
                for($i = 0; $i < $cantidad; $i++){
                    if($data[$i]['zona'] == $anterior || $i == 0){
                        $anterior = $data[$i]['zona'];
                        $nombre[$cont] = $data[$i]['zona'];
                        $suma[$cont] += $data[$i]['monto'];

                    }else{
                        $anterior = $data[$i]['zona'];
                        $cont++;
                        $suma[$cont];
                        $nombre[$cont] = $data[$i]['zona'];
                        $suma[$cont] += $data[$i]['monto'];
                    }
                }

                $sumaTotal = 0;
                for($i=0;$i<=$cont;$i++){
                    $sumaTotal += $suma[$i];
                }

                for($i=0;$i<=$cont;$i++){
                    $dato[$i]['porc'] = 100*($suma[$i]/$sumaTotal);
                    $dato[$i]['zona'] = $nombre[$i];
                    $dato[$i]['monto'] = $suma[$i];
                $graf[$nombre[$i]." = ".$suma[$i]."(".round($dato[$i]['porc'],2)."%)"] = $dato[$i]['porc'];
                }

            }

            echo "<h2>VENTAS POR ZONA: <b> ".$nombreGeografica[0]['nombrec']." >>> ".$nombreCategoria[0]['nombrec']."</b></h2>";

            if(count($data) != 0){
//                $color = array( '#ccff00', '#7498e9', '#000faa',);
                $color = array( '#ccff44', '#7492e9', '#0002aa',);
                $date=date('\a \l\a\s g:i a \d\e\l d.m.Y ');
                $this->AutoLoadLib(array('GoogChart','GoogChart.class'));
                $grafico=new GoogChart();
                $grafico->setChartAttrs(
                    array   (
                                'type' => 'pie',
                                'title' => $date,
                                'data' => $graf,
                                'size' => array(800,300),
                                'color' => $color
                            )
                );
                echo $grafico;
            }else{
                echo "<h3>NO SE HA ENCONTRADO NINGUNA ORDEN DE VENTA</h3>";
            }
        }
    }

    function reportePorcentajej5(){

        $zonaGeografica = !empty($_REQUEST['lstCategoriaPrincipal'])?$_REQUEST['lstCategoriaPrincipal']:"";
        $idCategoria = !empty($_REQUEST['lstZonaCobranza'])?$_REQUEST['lstZonaCobranza']:"";
        $fechaInicio = !empty($_REQUEST['txtFechaInicio'])?$_REQUEST['txtFechaInicio']:"";
        $fechaFin = !empty($_REQUEST['txtFechaFinal'])?$_REQUEST['txtFechaFinal']:"";
        //$idAnio = !empty($_REQUEST['lstAnio'])?$_REQUEST['lstAnio']:"";
        $idVendedor = !empty($_REQUEST['idVendedor'])?$_REQUEST['idVendedor']:"";


        $filtro = "ov.estado = 1 ";
        if(!empty($idVendedor)){
            $filtro.= " and a.idactor = ".$idVendedor;
        }
        if(!empty($fechaInicio)){
            $datei = explode('/',$fechaInicio);
            $fechai = $datei[0]."-".$datei[1]."-".$datei[2];
            $filtro .= " and ov.fordenventa >= '".$fechai."' ";
        }
        if(!empty($fechaFin)){
            $datef = explode('/',$fechaFin);
            $fechaf = $datef[0]."-".$datef[1]."-".$datef[2];
            $filtro .= " and ov.fordenventa <= '".$fechaf."' ";
        }


        $reporte = new Reporte();
        $zona = new Zona();
        $data = $reporte->obtenerVentaxZonaProcentaje($idCategoria,$filtro);
        $nombreGeografica = $zona->getnombreZona($zonaGeografica);
        $nombreCategoria = $zona->getnombreZona($idCategoria);

        if(count($data) != 0){

            $cantidad = count($data);
            $anterior = "zzz";
            $cont=0;
            $suma[$cont] = 0;
            for($i = 0; $i < $cantidad; $i++){
                if($data[$i]['zona'] == $anterior || $i == 0){
                    $anterior = $data[$i]['zona'];
                    $nombre[$cont] = $data[$i]['zona'];
                    $suma[$cont] += $data[$i]['monto'];

                }else{
                    $anterior = $data[$i]['zona'];
                    $cont++;
                    $suma[$cont];
                    $nombre[$cont] = $data[$i]['zona'];
                    $suma[$cont] += $data[$i]['monto'];
                }
            }

            $sumaTotal = 0;
            for($i=0;$i<=$cont;$i++){
                $sumaTotal += $suma[$i];
            }



            for($i=0;$i<=$cont;$i++){
                $dato[$i]['porc'] = 100*($suma[$i]/$sumaTotal);
                $dato[$i]['zona'] = $nombre[$i];
                $dato[$i]['monto'] = $suma[$i];
            $graf[$nombre[$i]." = ".$suma[$i]."(".round($dato[$i]['porc'],2)."%)"] = round($dato[$i]['porc'],2);
            }

        }

        $row[0]['name'] = 'nombre';
        $row[0]['gender'] = 22;
        $row[1]['name'] = 'hola';
        $row[1]['gender'] = 3;
//        foreach($graf as $m=>$n){
//            echo $m."-".$n;
//        }

        for($i = 0;$i<=$cont;$i++){
            echo $dato[$i]['zona']."/".$dato[$i]['monto'];
        }





        ?>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            //var person = {firstName:"John", lastName:4, age:46};
            function drawChart() {


                var data = google.visualization.arrayToDataTable([

                ['Name', 'Gender'],

                //datos;

//                ['Task', 4],
//                ['Work', 5],
                ['mm',54]

                <?php
                //echo $m;
                for($i=0;$i<2;$i++){
//                foreach($graf as $x=>$y){
                    ?>
//                    ['<?php //echo $dato[$i]['zona'];?>','<?php //echo $dato[$i]['monto'];?>'],
//                      ['<?php echo $row[$i]['name'];?>','<?php echo $row[$i]['gender'];?>'],
                    //['<?php echo $row['name'];?>','<?php echo $row['gender'];?>'],
                <?php
                } ?>


              ]);

              var options = {
                title: 'titulo'
              };

              var chart = new google.visualization.PieChart(document.getElementById('piechart3'));

              chart.draw(data, options);
            }
        </script>


    <?php

        echo "<h2>VENTAS POR ZONA: <b> ".$nombreGeografica[0]['nombrec']." >>> ".$nombreCategoria[0]['nombrec']."</b></h2>";

        if(count($data) != 0){
//            $color = array( '#ccff00', '#7498e9', '#000faa',);
//            $date=date('\a \l\a\s g:i a \d\e\l d.m.Y ');
//            $this->AutoLoadLib(array('GoogChart','GoogChart.class'));
//            $grafico=new GoogChart();
//            $grafico->setChartAttrs(
//                array   (
//                            'type' => 'pie',
//                            'title' => 'TITULO '.$date,
//                            'data' => $graf,
//                            'size' => array(800,300),
//                            'color' => $color
//                        )
//            );
//            echo $grafico;
            echo '<div id="piechart3" style="width: 900px; height: 500px;"></div>';
        }else{
            echo "<h3>NO SE HA ENCONTRADO NINGUNA ORDEN DE VENTA</h3>";
        }
    }
    /* FIN PORCENTAJE */

//    function reporteCuadroAvanceMensual(){
//        $this->view->show('/reporte/reporteCuadroAvanceMensual.phtml', $data);
//    }
    function reporteProductosTop(){
        $this->view->show('/reporte/reporteProductosTop.phtml',$data);
    }

    function listaProductosTop(){
        $linea = $_POST['linea'];
        $marca = $_POST['marca'];
        $inicio = $_POST['finicio'];
        $final = $_POST['ffinal'];

        $reporte = new Reporte();
        $dataProductosTop = $reporte->listaProductosTop($linea,$marca,$inicio,$final);
        $cantidadProductosTop = count($dataProductosTop);

        $productoAnt = 0;
        $sumaGanancia = 0;
        $cantidad = 0;
        $contador = 0;

        //echo $dataProductosTop;

        echo "<table>"
                . "<thead>"
                    . "<th>N°</th>"
                    . "<th>PRODUCTO</th>"
                    . "<th>CÓDIGO</th>"
                    . "<th>LINEA</th>"
                    . "<th>MARCA</th>"
                    . "<th>CANTIDAD</th>"
                    . "<th>UTILIDAD ACUMULADA</th>"
                    //. "<th>PORCENTAJE</th>"
                . "</thead>"
                . "<tbody>";
                    for($i=0;$i<=$cantidadProductosTop;$i++){

                        if($dataProductosTop[$i]['idproducto'] == $productoAnt || $i == 0){
//                            $producto = $dataProductosTop[$i]['producto'];
//                            $sumaGanancia += $dataProductosTop[$i]['utilidad'];
//                            $cantidad += $dataProductosTop[$i]['cantidad'];

                            $productoAnt = $dataProductosTop[$i]['idproducto'];
                            $resutProductosTop[$contador]['producto'] = $dataProductosTop[$i]['producto'];
                            $resutProductosTop[$contador]['codigo'] = $dataProductosTop[$i]['codigo'];
                            $resutProductosTop[$contador]['linea'] = $dataProductosTop[$i]['linea'];
                            $resutProductosTop[$contador]['marca'] = $dataProductosTop[$i]['marca'];
                            $resutProductosTop[$contador]['sumaGanancia'] += $dataProductosTop[$i]['utilidad'];
                            $resutProductosTop[$contador]['cantidad'] += $dataProductosTop[$i]['cantidad'];

                        }else{

                            echo "<tr>"
                            . "<td>".($contador+1)."</td>"
                            . "<td>".$resutProductosTop[$contador]['producto']."</td>"
                            . "<td>".$resutProductosTop[$contador]['codigo']."</td>"
                            . "<td>".$resutProductosTop[$contador]['linea']."</td>"
                            . "<td>".$resutProductosTop[$contador]['marca']."</td>"
                            . "<td>".$resutProductosTop[$contador]['cantidad']."</td>"
                            . "<td>".$resutProductosTop[$contador]['sumaGanancia']."</td>"
//                            . "<td>".$resutProductosTop[$contador]['porcentaje']."</td>"
                            . "</tr>";
                            $contador++;

//                            $producto = $dataProductosTop[$i]['producto'];
//                            $sumaGanancia += $dataProductosTop[$i]['utilidad'];
//                            $cantidad += $dataProductosTop[$i]['cantidad'];


//                            $cantidad = $dataProductosTop[$i]['cantidad'];;
//                            $sumaGanancia = $dataProductosTop[$i]['utilidad'];
//                            $productoAnt = $dataProductosTop[$i]['idproducto'];
//                            $producto = $dataProductosTop[$i]['producto'];

                            $productoAnt = $dataProductosTop[$i]['idproducto'];
                            $resutProductosTop[$contador]['producto'] = $dataProductosTop[$i]['producto'];
                            $resutProductosTop[$contador]['codigo'] = $dataProductosTop[$i]['codigo'];
                            $resutProductosTop[$contador]['linea'] = $dataProductosTop[$i]['linea'];
                            $resutProductosTop[$contador]['marca'] = $dataProductosTop[$i]['marca'];
                            $resutProductosTop[$contador]['sumaGanancia'] = $dataProductosTop[$i]['utilidad'];
                            $resutProductosTop[$contador]['cantidad'] = $dataProductosTop[$i]['cantidad'];
                        }

//                    echo "<tr>"
//                    . "<td>".($i+1)."</td>"
//                    . "<td>".$dataProductosTop[$i]['producto']."</td>"
//                    . "<td>".$dataProductosTop[$i]['codigo']."</td>"
//                    . "<td>".$dataProductosTop[$i]['linea']."</td>"
//                    . "<td>".$dataProductosTop[$i]['marca']."</td>"
//                    . "<td>".$dataProductosTop[$i]['cantidad']."</td>"
//                    . "<td>".$dataProductosTop[$i]['utilidad']."</td>"
//                    . "<td>".$dataProductosTop[$i]['porcentaje']."</td>"
//                    . "</tr>";

                    }
                echo "</tbody>"
        . "</table>";

        echo $cantidadProductosTop;
        //var_dump($resutProductosTop);
        //sort($resutProductosTop);
        //array_multisort($resutProductosTop[]['sumaGanancia']);
        //var_dump($resutProductosTop);

        $cantidadResultPT = count($resutProductosTop);


        echo "<table>"
                . "<thead>"
                    . "<th>N°</th>"
                    . "<th>PRODUCTO</th>"
                    . "<th>CÓDIGO</th>"
                    . "<th>LINEA</th>"
                    . "<th>MARCA</th>"
                    . "<th>CANTIDAD</th>"
                    . "<th>UTILIDAD ACUMULADA</th>"
                    . "<th>PORCENTAJE</th>"
                . "</thead>"
                . "<tbody>";




                    for($i=0;$i<$cantidadResultPT;$i++){

                        //$contador++;
                        echo "<tr>"
                        . "<td>".($i+1)."</td>"
                        . "<td>".$resutProductosTop[$i]['producto']."</td>"
                        . "<td>".$resutProductosTop[$i]['codigo']."</td>"
                        . "<td>".$resutProductosTop[$i]['linea']."</td>"
                        . "<td>".$resutProductosTop[$i]['marca']."</td>"
                        . "<td>".$resutProductosTop[$i]['cantidad']."</td>"
                        . "<td>".$resutProductosTop[$i]['sumaGanancia']."</td>"
                        . "<td>".$resutProductosTop[$i]['porcentaje']."</td>"
                        . "</tr>";

//                    echo "<tr>"
//                    . "<td>".($i+1)."</td>"
//                    . "<td>".$dataProductosTop[$i]['producto']."</td>"
//                    . "<td>".$dataProductosTop[$i]['codigo']."</td>"
//                    . "<td>".$dataProductosTop[$i]['linea']."</td>"
//                    . "<td>".$dataProductosTop[$i]['marca']."</td>"
//                    . "<td>".$dataProductosTop[$i]['cantidad']."</td>"
//                    . "<td>".$dataProductosTop[$i]['utilidad']."</td>"
//                    . "<td>".$dataProductosTop[$i]['porcentaje']."</td>"
//                    . "</tr>";

                    }
                echo "</tbody>"
        . "</table>";

    }

    function exportarExcelProductosTop(){

        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=abc.xls");  //File name extension was wrong
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);



        //EL CONTENIDO:

        $linea = $_POST['linea'];
        $marca = $_POST['marca'];
        $inicio = $_POST['finicio'];
        $final = $_POST['ffinal'];

        $reporte = new Reporte();
        $dataProductosTop = $reporte->listaProductosTop($linea,$marca,$inicio,$final);
        $cantidadProductosTop = count($dataProductosTop);

        $productoAnt = 0;
        $sumaGanancia = 0;
        $cantidad = 0;
        $contador = 0;

        //echo $dataProductosTop;

        echo "<table>"
                . "<thead>"
                    . "<th>N°</th>"
                    . "<th>PRODUCTO</th>"
                    . "<th>CÓDIGO</th>"
                    . "<th>LINEA</th>"
                    . "<th>MARCA</th>"
                    . "<th>CANTIDAD</th>"
                    . "<th>UTILIDAD ACUMULADA</th>"
                    //. "<th>PORCENTAJE</th>"
                . "</thead>"
                . "<tbody>";
                    for($i=0;$i<=$cantidadProductosTop;$i++){

                        if($dataProductosTop[$i]['idproducto'] == $productoAnt || $i == 0){
//                            $producto = $dataProductosTop[$i]['producto'];
//                            $sumaGanancia += $dataProductosTop[$i]['utilidad'];
//                            $cantidad += $dataProductosTop[$i]['cantidad'];

                            $productoAnt = $dataProductosTop[$i]['idproducto'];
                            $resutProductosTop[$contador]['producto'] = $dataProductosTop[$i]['producto'];
                            $resutProductosTop[$contador]['codigo'] = $dataProductosTop[$i]['codigo'];
                            $resutProductosTop[$contador]['linea'] = $dataProductosTop[$i]['linea'];
                            $resutProductosTop[$contador]['marca'] = $dataProductosTop[$i]['marca'];
                            $resutProductosTop[$contador]['sumaGanancia'] += $dataProductosTop[$i]['utilidad'];
                            $resutProductosTop[$contador]['cantidad'] += $dataProductosTop[$i]['cantidad'];

                        }else{

                            echo "<tr>"
                            . "<td>".($contador+1)."</td>"
                            . "<td>".$resutProductosTop[$contador]['producto']."</td>"
                            . "<td>".$resutProductosTop[$contador]['codigo']."</td>"
                            . "<td>".$resutProductosTop[$contador]['linea']."</td>"
                            . "<td>".$resutProductosTop[$contador]['marca']."</td>"
                            . "<td>".$resutProductosTop[$contador]['cantidad']."</td>"
                            . "<td>".$resutProductosTop[$contador]['sumaGanancia']."</td>"
                            //. "<td>".$resutProductosTop[$contador]['porcentaje']."</td>"
                            . "</tr>";
                            $contador++;

//                            $producto = $dataProductosTop[$i]['producto'];
//                            $sumaGanancia += $dataProductosTop[$i]['utilidad'];
//                            $cantidad += $dataProductosTop[$i]['cantidad'];


//                            $cantidad = $dataProductosTop[$i]['cantidad'];;
//                            $sumaGanancia = $dataProductosTop[$i]['utilidad'];
//                            $productoAnt = $dataProductosTop[$i]['idproducto'];
//                            $producto = $dataProductosTop[$i]['producto'];

                            $productoAnt = $dataProductosTop[$i]['idproducto'];
                            $resutProductosTop[$contador]['producto'] = $dataProductosTop[$i]['producto'];
                            $resutProductosTop[$contador]['codigo'] = $dataProductosTop[$i]['codigo'];
                            $resutProductosTop[$contador]['linea'] = $dataProductosTop[$i]['linea'];
                            $resutProductosTop[$contador]['marca'] = $dataProductosTop[$i]['marca'];
                            $resutProductosTop[$contador]['sumaGanancia'] = $dataProductosTop[$i]['utilidad'];
                            $resutProductosTop[$contador]['cantidad'] = $dataProductosTop[$i]['cantidad'];
                        }

//                    echo "<tr>"
//                    . "<td>".($i+1)."</td>"
//                    . "<td>".$dataProductosTop[$i]['producto']."</td>"
//                    . "<td>".$dataProductosTop[$i]['codigo']."</td>"
//                    . "<td>".$dataProductosTop[$i]['linea']."</td>"
//                    . "<td>".$dataProductosTop[$i]['marca']."</td>"
//                    . "<td>".$dataProductosTop[$i]['cantidad']."</td>"
//                    . "<td>".$dataProductosTop[$i]['utilidad']."</td>"
//                    . "<td>".$dataProductosTop[$i]['porcentaje']."</td>"
//                    . "</tr>";

                    }
                echo "</tbody>"
        . "</table>";
    }

    function reporteControlCobranzas(){
        $this->view->show('reporte/reporteControlCobranzas.phtml', $data);

    }

    function mostrarControlCobranzas(){

    }

    function reporteFinStock(){
        $this->view->show('reporte/reporteFinStock.phtml',$data);
    }

    function listadoStock(){
        $producto = new Producto();
        $dataProducto = $producto->getTotalProductos();
        $cantProducto = count($dataProducto);
        if ($_REQUEST['fechaFinal'] != "") {
            $fechaFinal = date('Y-m-d', strtotime($_REQUEST['fechaFinal']));
        }else{
            $fechaFinal = date('Y-m-d');
        }
//        echo 'fecha: '.$fechaFinal;
//        echo $cantProducto;

        echo '<table>'
            . '<thead>'
                . '<th>ID</th>'
                . '<th>PRODUCTO</th>'
                . '<th>CÓDIGO</th>'
                . '<th>STOCK</th>'
                //. '<th>VENTAS</th>'
                //. '<th>COMPRAS</th>'
                //. '<th>DEVOLUCIÓN</th>'
                //. '<th>CANT. FINAL</th>'
                . '<th>PRECIO LISTA</th>'
                . '<th>MONTO(DÓLARES)</th>'
                . '<th>MONTO(SOLES)</th>'
            . '</thead>'
            . '<tbody>';
        $sumaDolares = 0;
        $sumaSoles = 0;
        $sumaStock = 0;
//        $sumaPrecios = 0;
        for($i=0; $i<$cantProducto;$i++)
        {
            $ventas = $producto->sumaTotalProductosVenta($fechaFinal,$dataProducto[$i]['idproducto']);
            $compras = $producto->sumaTotalProductosCompra($fechaFinal,$dataProducto[$i]['idproducto']);
            $compra = empty($compras[0]['compras'])? 0: $compras[0]['compras'];
            $devoluciones = $producto->sumaTotalDevolucion($fechaFinal,$dataProducto[$i]['idproducto']);
            $devolucion = empty($devoluciones[0]['cantidad'])? 0: $devoluciones[0]['cantidad'];

            $cantFinal = $dataProducto[$i]['stockdisponible']+$ventas-$compra-$devolucion;

            $montoDolares = round($cantFinal*$dataProducto[$i]['preciolistadolares'],2);
            $montoSoles = round($montoDolares*3.34,2);
            $sumaDolares += $montoDolares;
            $sumaSoles += $montoSoles;
//            $sumaStock += $dataProducto[$i]['stockdisponible'];
            if($cantFinal>0){
            echo '<tr>'
                    . '<td>'.($i+1).'</td>'
                    . '<td>'.$dataProducto[$i]['nompro'].'</td>'
                    . '<td>'.$dataProducto[$i]['codigopa'].'</td>'
                    //. '<td>'.$dataProducto[$i]['stockdisponible'].'</td>'
                    //. '<td>'.$ventas.'</td>'
                    //. '<td>'.$compra.'</td>'
                    //. '<td>'.$devolucion.'</td>'
                    . '<td>'.$cantFinal.'</td>'
                    . '<td>'.$dataProducto[$i]['preciolistadolares'].'</td>'
                    . '<td>'.$montoDolares.'</td>'
                    . '<td>'.$montoSoles.'</td>'
               . '</tr>';
            }
        }

//        $promedioPrecios = round($sumaPrecios/$cantProducto,2);

        echo  '<tr>'
                . '<td colspan="3" style="text-align:center;">TOTAL</td>'
                . '<td></td>'
                . '<td></td>'
//                . '<td></td>'
//                . '<td></td>'
//                . '<td></td>'
//                . '<td></td>'
                . '<td>'.$sumaDolares.'</td>'
                . '<td>'.$sumaSoles.'</td>'
            . '</tr>';

        echo  '</tbody>'
        . '</table>';

    }

    function listaControlCobranzas(){
        $idCliente = $_REQUEST['id'];
        $reporte = new Reporte();
        $cliente = new Cliente();
        $ordenventa = new Ordenventa();
        //obtener las ordenes de venta del cliente
        $dataCliente = $reporte->infoOrdenVentaxCliente($idCliente);
        $contDataCliente = count($dataCliente);

        for ($i = 0; $i < $contDataCliente; $i++) {
            //Inicio contenido del for...

            $dataDocumentoFactura = $reporte->infoDocumento($dataCliente[$i]['idOrdenventa'],1);
            $numFac = '';
            //var_dump($dataDocumentoFactura);
            for($j = 0; $j<count($dataDocumentoFactura); $j++){
                if($j == (count($dataDocumentoFactura)-1)){
                    $numFac .= strval($dataDocumentoFactura[$j]['numdoc']);
                }else{
                    $numFac .= strval($dataDocumentoFactura[$j]['numdoc'])."-";
                }
            }


            $dataDocumeGuia = $reporte->infoDocumento($dataCliente[$i]['idOrdenventa'],4);
            $numGuia = '';
            for($j = 0; $j<count($dataDocumentoFactura); $j++){
                if($j == (count($dataDocumeGuia)-1)){
                    $numGuia .= strval($dataDocumeGuia[$j]['numdoc']);
                }else{
                    $numGuia .= strval($dataDocumeGuia[$j]['numdoc'])."-";
                }

            }

            echo "<tr>"
                    . "<td>".($i+1)."/".$dataCliente[$i]['idOrdenventa']."</td>"
                    . "<td>".$dataCliente[$i]['codigo']."</td>"
                    . "<td>".$numGuia."</td>"
                    . "<td>".$numFac."</td>"
                    . "<td>".$dataCliente[$i]['fordenventa']."</td>"
                    . "<td>".$dataCliente[$i]['fordenventa']."</td>"
                    . "<td>".$dataCliente[$i]['fordenventa']."</td>"
                    . "<td>".$dataCliente[$i]['fordenventa']."</td>"
                    . "<td>".$dataCliente[$i]['fordenventa']."</td>";


            $dataDetCobro = $reporte->infoLetraxOrdenVenta($dataCliente[$i]['idOrdenventa']);
            $contLetra = count($dataDetCobro);

            for($j=0;$j<$contLetra;$j++){
                echo "<td>".$dataDetCobro[$j]['fvencimiento']."</td>"
                    . "<td>".$dataDetCobro[$j]['numeroletra']."</td>"
                    . "<td>".$dataDetCobro[$j]['situacion']."</td>";
            }

            echo "</tr>";



            //Fin contenido del for...
        }
    }

    function reporte_cobranzas_vista(){
        $zona = $this->AutoLoadModel('zona');
        $actor = $this->AutoLoadModel('actorrol');
        $tipoCobranza = $this->AutoLoadModel('tipocobranza');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['hijo'] = $zona->listacategoriaHijo();
        $data['zona'] = $zona->listadoTotalZona();
        $data['tipocobranza'] = $tipoCobranza->listaNueva();
        $data['vendedor'] = $actor->actoresxRolxNombre(25);
        $data['cobrador'] = $actor->actoresxRolxNombre(28);
        $this->view->show('/reporte/reporte_cobranzas.phtml', $data);
    }

    function reporte_cobranzas(){
        // Crearción de instacias de reporte, tipocobranza, ordengasto, tipocobro y movimiento:
        $reporte = $this->AutoLoadModel('reporte');
        $tipo = $this->AutoLoadModel('tipocobranza');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $tipoCobroIni = $this->configIniTodo('TipoCobro');
        $movimiento = $this->AutoLoadModel('movimiento');
        // recepción de variables:
        $idzona = $_REQUEST['idzona'];
        $idcategoriaprincipal = $_REQUEST['idcategoriaprincipal'];
        $idcategoria = $_REQUEST['idcategoria'];
        $idvendedor = $_REQUEST['idvendedor'];
        $idtipocobranza = $_REQUEST['idtipocobranza'];
        $idtipocobro = $_REQUEST['idtipocobro'];
        $fechaInicio = $_REQUEST['fechaInicio'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        $pendiente = $_REQUEST['pendiente'];
        $cancelado = $_REQUEST['cancelado'];
        $octava = $_REQUEST['octava'];
        $novena = $_REQUEST['novena'];
        $idcobrador = $_REQUEST['idcobrador'];
        $IdCliente = $_REQUEST['IdCliente'];
        $IdOrdenVenta = $_REQUEST['IdOrdenVenta'];
        $recepcionLetras = $_REQUEST['recepcionLetras'];

    }

    public function ciclodevida() {
        $this->view->show('/reporte/ciclovida.phtml');
    }

    function ciclodevida_consulta() {/**/
        set_time_limit(1000);
        $id = $_REQUEST['idcontenedor'];
        $data['porcifventas'] = $this->configIni('Parametros', 'PorCifVentas');
        $ordenCompra = $this->AutoLoadModel('ordencompra');
        $reporte = $this->AutoLoadModel('reporte');
        $detalleOrdenCompra = $this->AutoLoadModel('detalleordencompra');
        $detalleordenventa = $this->AutoLoadModel('detalleordenventa');
        $dataOrdenCompra = $ordenCompra->OrdenCuadroUtilidad($id);
        $dataDetalleordenCompra = $detalleOrdenCompra->listaDetalleOrdenCompra($id);
        $cantidad = count($dataDetalleordenCompra);
        $tipocambio = $dataOrdenCompra[0]['tipocambiovigente'];
        
        $porcentaje = (($data['porcifventas'] + 100) / 100);
        if ($dataOrdenCompra[0]['cifcpa'] > 0) {
            $porcentaje = (($dataOrdenCompra[0]['cifcpa'] + 100) / 100);
        }
                            
        
        $totalUtilidad = 0;
        $utilidadDolares = 0;
        $utilidadDolaresxProducto = 0;

        $body = "";
        $foot = "";

        $documento = $this->AutoLoadModel('documento');
        $ordencobro = New OrdenCobro();
        $detalleOrdenCobro = New DetalleOrdenCobro();
        $contadoTOTAL = 0;
        $creditoTOTAL = 0;
        $letrasTOTAL = 0;
        $deudaTOTAL = 0;

        for ($i = 0; $i < $cantidad; $i++) {
            $cont = 0;
            $salidas = 0;
            $entradas = 0;
            $contado = 0;
            $credito = 0;
            $letras = 0;
            $deuda = 0;
            $PorContado = 0;
            $PorCredito = 0;
            $PorLetras = 0;
            $PorDeuda = 0;
            $productos = $reporte->reporteKardexProduccion("", "", $dataDetalleordenCompra[$i]['idproducto'], "", "");
            for ($x = 0; $x < count($productos); $x++) {
                if ($productos[$x]['idordencompra'] == $id) {
                    //$idmovimiento=$datos[$i]['codigooc'];
                    $a = $x + 1;
                    break;
                }
            }
            for ($y = $a; $y < count($productos); $y++) {
                $TempContado = 0;
                $TempCredito = 0;
                $TempLetras = 0;
                $TempDeuda = 0;
                if ($productos[$y]['codigooc'] == "" and $productos[$y]['codigov'] != "") {
                    $cont++;
                    if ($productos[$y]['tipo movimiento'] == "Salidas") {
                        $salidas += $productos[$y]['cantidad'];

                        $filtro = " nombredoc=4 ";
                        $datadocumento1 = $documento->buscadocumentoxordenventa($productos[$y]['idordenventa'], $filtro, true);
                        if (count($datadocumento1) > 0 && !empty($datadocumento1[0]['montofacturado'])) {
                            $montoapagar = $datadocumento1[0]['montofacturado'];
                        } else {
                            $filtro = " nombredoc=1 ";
                            $datadocumento2 = $documento->buscadocumentoxordenventa($productos[$y]['idordenventa'], $filtro, true);
                            if (count($datadocumento2) > 0 && !empty($datadocumento2[0]['montofacturado'])) {
                                $montoapagar = $datadocumento2[0]['montofacturado'];
                            } else {
                                $filtro = " nombredoc=2 ";
                                $datadocumento3 = $documento->buscadocumentoxordenventa($productos[$y]['idordenventa'], $filtro, true);
                                if (count($datadocumento3) > 0 && !empty($datadocumento3[0]['montofacturado'])) {
                                    $montoapagar = $datadocumento3[0]['montofacturado'];
                                } else {
                                    $montoapagar = $productos[$y]['importeov'];
                                }
                            }
                        }
                        $dataOrdenCobro = $ordencobro->listarxguia($productos[$y]['idordenventa']);
                        for ($n = count($dataOrdenCobro) - 1; $n >= 0; $n--) {
                            $dataDetalleOrdenCobro = $detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$n]['idordencobro']);
                            $tamanio = count($dataDetalleOrdenCobro);
                            for ($m = 0; $m < $tamanio; $m++) {
                                if (($dataDetalleOrdenCobro[$m]['situacion'] == 'cancelado')) {
                                    if ($dataDetalleOrdenCobro[$m]['formacobro'] == '1') {
                                        $TempContado += $dataDetalleOrdenCobro[$m]['importedoc'];
                                    } else if ($dataDetalleOrdenCobro[$m]['formacobro'] == '2') {
                                        $TempCredito += $dataDetalleOrdenCobro[$m]['importedoc'];
                                    } else {
                                        $TempLetras += $dataDetalleOrdenCobro[$m]['importedoc'];
                                    }
                                }
                            }
                            $TempDeuda += $dataOrdenCobro[$n]['saldoordencobro'];
                        }

                        //$montoapagar = $TempContado + $TempCredito + $TempLetras + $TempDeuda;
                        $PorContado = (100 * $TempContado) / $montoapagar;
                        if ($PorContado > 100)
                            $PorContado = 100;
                        $PorCredito = (100 * $TempCredito) / $montoapagar;
                        if ($PorCredito > 100)
                            $PorCredito = 100;
                        $PorLetras = (100 * $TempLetras) / $montoapagar;
                        if ($PorLetras > 100)
                            $PorLetras = 100;
                        $PorDeuda = (100 * $TempDeuda) / $montoapagar;
                        if ($PorDeuda > 100)
                            $PorDeuda = 100;
                        //echo "1: " . number_format($TempContado,2) . " -  " . number_format($TempCredito,2) . " - " . number_format($TempLetras,2) . " - " . number_format($TempDeuda,2) . "<br>";
                        //echo "2: " . number_format($PorContado,2) . " -  " . number_format($PorCredito,2) . " - " . number_format($PorLetras,2) .  " - " . number_format($PorDeuda,2) . " :::: " . number_format($montoapagar,2) . "<br><br><br>";
                        $dataP = $detalleordenventa->listaDetalleOrdenVentaxProducto($productos[$y]['idordenventa'], $dataDetalleordenCompra[$i]['idproducto']);

                        $contado += (($dataP[0]['cantdespacho'] - $dataP[0]['cantdevuelta']) * $dataP[0]['preciofinal']) * ($PorContado / 100);
                        $credito += (($dataP[0]['cantdespacho'] - $dataP[0]['cantdevuelta']) * $dataP[0]['preciofinal']) * ($PorCredito / 100);
                        $letras += (($dataP[0]['cantdespacho'] - $dataP[0]['cantdevuelta']) * $dataP[0]['preciofinal']) * ($PorLetras / 100);
                        $deuda += (($dataP[0]['cantdespacho'] - $dataP[0]['cantdevuelta']) * $dataP[0]['preciofinal']) * ($PorDeuda / 100);
                    }
                    else {
                        $entradas += $productos[$y]['cantidad'];
                    }
                } else {
                    break;
                }
            }

            $productosVendidos = $salidas - $entradas;
            if ($productosVendidos > $dataDetalleordenCompra[$i]['cantidadrecibidaoc']) {
                $productosVendidos = $dataDetalleordenCompra[$i]['cantidadrecibidaoc'];
            }
            if ($dataDetalleordenCompra[$i]['precio_listadolares'] > 0) {
                $preciolistaDolares = $dataDetalleordenCompra[$i]['precio_listadolares'];
            } else {
                $preciolistaDolares = $dataDetalleordenCompra[$i]['preciolista'] / $tipocambio;
            }
            $cifventas = $dataDetalleordenCompra[$i]['fobdoc'] * $porcentaje;
            //$utilidadporcentaje=($dataDetalleordenCompra[$i][preciotopedolares]-$cifventas)*100/$cifventas;

            $totalUtilidad += $utilidadDolares;
            $descuento13 = $preciolistaDolares - ($preciolistaDolares * 0.13);
            $descuento5 = $descuento13 - ($descuento13 * 0.05);
            $descuento95 = $descuento5 - ($descuento5 * 0.095);
            $precioVenta = $descuento95 - ($descuento95 * 0.05);
            $utilidadReal = (($precioVenta - $cifventas) / $cifventas) * 100;
            $utilidadDolaresxProducto = ($precioVenta - $cifventas) * $dataDetalleordenCompra[$i]['cantidadrecibidaoc'];
            $utilidadTotal += $utilidadDolaresxProducto;
            //$precioVenta=((($preciolistaDolares-($preciolistaDolares*0.13))-(($preciolistaDolares-($preciolistaDolares*0.13))*0.05))-((($preciolistaDolares-($preciolistaDolares*0.13))-(($preciolistaDolares-($preciolistaDolares*0.13))*0.05))*0.095))-(((($preciolistaDolares-($preciolistaDolares*0.13))-(($preciolistaDolares-($preciolistaDolares*0.13))*0.05))-((($preciolistaDolares-($preciolistaDolares*0.13))-(($preciolistaDolares-($preciolistaDolares*0.13))*0.05))*0.095))*0.05);
            //$deuda = ($preciolistaDolares*$dataDetalleordenCompra[$i]['cantidadrecibidaoc'])-($contado+$credito+$letras);
            //if ($deuda < 0) $deuda = 0;

            $contadoTOTAL += $contado;
            $creditoTOTAL += $credito;
            $letrasTOTAL += $letras;
            $deudaTOTAL += $deuda;

            $body .= "<tr>";
            $body .= "<td>";
            $body .= ($i + 1);
            $body .= "</td>";
            $body .= "<td>" . $dataDetalleordenCompra[$i]['codigopa'] . "</td>";
            $body .= "<td class='text-300'>" . $dataDetalleordenCompra[$i]['nompro'] . "</td>";
            $body .= "<td>" . $dataDetalleordenCompra[$i]['cantidadrecibidaoc'] . "</td>";
            $body .= "<td>" . number_format($dataDetalleordenCompra[$i]['preciocosto'] / $tipocambio, 2) . "</td>";
            $body .= "<td>" . number_format($dataDetalleordenCompra[$i]['fobdoc'] * $porcentaje, 2) . "</td>";
            $body .= "<td>" . number_format($tipocambio, 2) . "</td>";
            $body .= "<td>" . $dataDetalleordenCompra[$i]['preciotopedolares'] . "</td>";
            $body .= "<td>" . number_format($preciolistaDolares, 2) . "</td>";
            $body .= "<td>" . number_format($dataDetalleordenCompra[$i]['preciotope'], 2) . "</td>";
            $body .= "<td>" . number_format($dataDetalleordenCompra[$i]['preciolista'], 2) . "</td>";
            $body .= "<td>" . $productosVendidos . "</td>";
            $body .= "<td>" . ($dataDetalleordenCompra[$i]['cantidadrecibidaoc'] - $productosVendidos) . "</td>";
            $body .= "<td>" . number_format($contado, 2) . "</td>";
            $body .= "<td>" . number_format($credito, 2) . "</td>";
            $body .= "<td>" . number_format($letras, 2) . "</td>";
            $body .= "<td>" . number_format($deuda, 2) . "</td>";
            $body .= "<td>" . number_format($utilidadReal, 1) . "%" . "</td>";
            $body .= "<td>" . number_format($precioVenta, 2) . "</td>";
            $body .= "<td>" . number_format($utilidadDolaresxProducto, 2) . "</td>";
            $body .= "</tr>";
        }
        $foot .= "<tr>"
                . "<td colspan='13'></td>"
                . "<th style='background:#0AC557'>$. " . number_format($contadoTOTAL, 2) . "</th>"
                . "<th style='background:#0AC557'>$. " . number_format($creditoTOTAL, 2) . "</th>"
                . "<th style='background:#0AC557'>$. " . number_format($letrasTOTAL, 2) . "</th>"
                . "<th style='background:#0AC557'>$. " . number_format($deudaTOTAL, 2) . "</th>"
                . "<td colspan='2'></td>"
                . "<th style='background:#cabb0f'>$. " . number_format($utilidadTotal, 2) . "</th>"
                . "</tr>";
        $respuesta['body'] = $body;
        $respuesta['foot'] = $foot;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($respuesta);
    }

    function reporte_cobranzas_enviar(){
        $lstcategoriaprincipal = $_POST['lstCategoriaPrincipal'];
        $lstcategoria = $_REQUEST['lstCategoria'];
        $lstZona = $_REQUEST['lstZona'];
        //echo "<tr><td>INICIO".$lstcategoriaprincipal.$lstcategoria.$lstZona."FIN</td></tr>";
        $reporte = new Reporte();
        $data = $reporte->get_zonas($lstZona,$lstcategoria,$lstcategoriaprincipal);
        //echo "<tr><td>".$data."FIN</td></tr>";
        $contado = 1;
        $credito = 2;
        $letras = 3;

        for($i = 0; $i <count($data); $i++)
        {
            echo "<tr>";
                echo "<td rowspan='2'>".$data[$i]['nombrezona']."</td>";

                /*INICIO DE TIPO DE COBRO AL CONTADO*/

                $sumaContadoCanceladoDolares = 0;
                $sumaContadoPendienteDolares = 0;
                $sumaContadoMorosoDolares = 0;
                $sumaContadoCanceladoSoles = 0;
                $sumaContadoPendienteSoles = 0;
                $sumaContadoMorosoSoles = 0;
                //Conseguir los montos de los cobros:
                $data1 = $reporte->get_detalle_orden_cobro($data[$i]['idzona'],$contado);
                for($j = 0;$j<=count($data1);$j++){
                    //si idmoneda == a dolares?
                    if($data1[$j]['idmoneda'] == 2){
                        if($data1[$j]['situacion'] == 'cancelado'){
                            $sumaContadoCanceladoDolares += $data1[$j]['importedoc'];
                        }else if($data1[$j]['situacion'] == ''){
                            if($data1[$j]['importedoc'] == $data1[$j]['saldodoc']){
                                //vencio?
                                $fecha_actual = strtotime(date("d-m-Y",time()));
                                $fecha_vencimiento = strtotime($data1[$j]['fvencimiento']);
                                if($fecha_vencimiento < $fecha_actual){
                                    $sumaContadoPendienteDolares += $data1[$j]['importedoc'];
                                }else{
                                    $sumaContadoMorosoDolares += $data1[$j]['importedoc'];
                                }
                            }else{
                                //cuanto?
                                $sumaContadoCanceladoDolares += ($data1[$j]['importedoc']-$data1[$j]['saldoc']);
                                //venció?
                                $fecha_actual = strtotime(date("d-m-Y",time()));
                                $fecha_vencimiento = strtotime($data1[$j]['fvencimiento']);
                                if($fecha_vencimiento < $fecha_actual){
                                    $sumaContadoPendienteDolares += $data1[$j]['saldodoc'];
                                }else{
                                    $sumaContadoMorosoDolares += $data1[$j]['saldodoc'];
                                }
                            }
                        }
                    }else{
                        //en caso que sea soles:
                        if($data1[$j]['situacion'] == 'cancelado'){
                            $sumaContadoCanceladoSoles += $data1[$j]['importedoc'];
                        }else if($data1[$j]['situacion'] == ''){
                            if($data1[$j]['importedoc'] == $data1[$j]['saldodoc']){
                                //vencio?
                                $fecha_actual = strtotime(date("d-m-Y",time()));
                                $fecha_vencimiento = strtotime($data1[$j]['fvencimiento']);
                                if($fecha_vencimiento < $fecha_actual){
                                    $sumaContadoPendienteSoles += $data1[$j]['importedoc'];
                                }else{
                                    $sumaContadoMorosoSoles += $data1[$j]['importedoc'];
                                }
                            }else{
                                //cuanto?
                                $sumaContadoCanceladoSoles += ($data1[$j]['importedoc']-$data1[$j]['saldoc']);
                                //venció?
                                $fecha_actual = strtotime(date("d-m-Y",time()));
                                $fecha_vencimiento = strtotime($data1[$j]['fvencimiento']);
                                if($fecha_vencimiento < $fecha_actual){
                                    $sumaContadoPendienteSoles += $data1[$j]['saldodoc'];
                                }else{
                                    $sumaContadoMorosoSoles += $data1[$j]['saldodoc'];
                                }
                            }
                        }
                    }
                }

                /*FIN DE TIPO DE COBRO AL CONTADO*/

                /*INICIO DE TIPO DE COBRO AL CREDITO*/
                $sumaCreditoCanceladoDolares = 0;
                $sumaCreditoPendienteDolares = 0;
                $sumaCreditoMorosoDolares = 0;
                $sumaCreditoCanceladoSoles = 0;
                $sumaCreditoPendienteSoles = 0;
                $sumaCreditoMorosoSoles = 0;
                //Conseguir los montos de los cobros:
                $data1 = $reporte->get_detalle_orden_cobro($data[$i]['idzona'],$credito);
                for($j = 0;$j<=count($data1);$j++){
                    //si idmoneda == a dolares?
                    if($data1[$j]['idmoneda'] == 2){
                        if($data1[$j]['situacion'] == 'cancelado'){
                            $sumaCreditoCanceladoDolares += $data1[$j]['importedoc'];
                        }else{
                            if($data1[$j]['importedoc'] == $data1[$j]['saldodoc']){
                                //vencio?
                                $fecha_actual = strtotime(date("d-m-Y",time()));
                                $fecha_vencimiento = strtotime($data1[$j]['fvencimiento']);
                                if($fecha_vencimiento < $fecha_actual){
                                    $sumaCreditoPendienteDolares += $data1[$j]['importedoc'];
                                }else{
                                    $sumaCreditoMorosoDolares += $data1[$j]['importedoc'];
                                }
                            }else{
                                //cuanto?
                                $sumaCreditoCanceladoDolares += ($data1[$j]['importedoc']-$data1[$j]['saldoc']);
                                //venció?
                                $fecha_actual = strtotime(date("d-m-Y",time()));
                                $fecha_vencimiento = strtotime($data1[$j]['fvencimiento']);
                                if($fecha_vencimiento < $fecha_actual){
                                    $sumaCreditoPendienteDolares += $data1[$j]['saldodoc'];
                                }else{
                                    $sumaCreditoMorosoDolares += $data1[$j]['saldodoc'];
                                }
                            }
                        }
                    }else{
                        //en caso que sea soles:
                        if($data1[$j]['situacion'] == 'cancelado'){
                            $sumaCreditoCanceladoSoles += $data1[$j]['importedoc'];
                        }else{
                            if($data1[$j]['importedoc'] == $data1[$j]['saldodoc']){
                                //vencio?
                                $fecha_actual = strtotime(date("d-m-Y",time()));
                                $fecha_vencimiento = strtotime($data1[$j]['fvencimiento']);
                                if($fecha_vencimiento < $fecha_actual){
                                    $sumaCreditoPendienteSoles += $data1[$j]['importedoc'];
                                }else{
                                    $sumaCreditoMorosoSoles += $data1[$j]['importedoc'];
                                }
                            }else{
                                //cuanto?
                                $sumaCreditoCanceladoSoles += ($data1[$j]['importedoc']-$data1[$j]['saldoc']);
                                //venció?
                                $fecha_actual = strtotime(date("d-m-Y",time()));
                                $fecha_vencimiento = strtotime($data1[$j]['fvencimiento']);
                                if($fecha_vencimiento < $fecha_actual){
                                    $sumaCreditoPendienteSoles += $data1[$j]['saldodoc'];
                                }else{
                                    $sumaCreditoMorosoSoles += $data1[$j]['saldodoc'];
                                }
                            }
                        }
                    }
                }

                /*FIN DE TIPO DE COBRO AL CREDITO*/

                /*INICIO DE TIPO DE COBRO DE LETRAS*/
                $sumaLetraBancoDolares = 0;
                $sumaLetraxFirmarDolares = 0;
                $sumaLetraProtestadaDolares = 0;
                $sumaLetraBancoSoles = 0;
                $sumaLetraxFirmarSoles = 0;
                $sumaLetraProtestadaSoles = 0;

                $data2 = $reporte->get_detalle_letras2($data[$i]['idzona']);
                for($j=0;$j<count($data2);$j++){
                    if($data2[$j]['idmoneda'] == 2){
                        //
                        if(empty($data2[$j]['recepcionletras'])){
                            $sumaLetraxFirmarDolares += $data2[$j]['saldodoc'];
                        }else{
                            $sumaLetraBancoDolares += $data2[$j]['saldodoc'];
                        }
                    }else{
                        //
                        if(empty($data2[$j]['recepcionletras'])){
                            $sumaLetraxFirmarSoles += $data2[$j]['saldodoc'];
                        }else{
                            $sumaLetraBancoSoles += $data2[$j]['saldodoc'];
                        }
                    }
                }

                $data3 = $reporte->resumenLetrasProtestadas();
                for($j=0;$j<count($data2);$j++){
                    if($data2[$j]['idmoneda'] == 2){
                        $sumaLetraProtestadaDolares += $data3[$j]['saldodoc'];
                    }else{
                        $sumaLetraProtestadaSoles += $data3[$j]['saldodoc'];
                    }
                }

                /*FIN DE TIPO DE COBRO DE LETRAS*/

                echo "<td></td>";
                echo "<td>".$sumaContadoCanceladoDolares."</td>";
                echo "<td></td>";
                echo "<td>".$sumaContadoPendienteDolares."</td>";
                echo "<td></td>";
                echo "<td>".$sumaContadoMorosoDolares."</td>";

                echo "<td></td>";
                echo "<td>".$sumaCreditoCanceladoDolares."</td>";
                echo "<td></td>";
                echo "<td>".$sumaCreditoPendienteDolares."</td>";
                echo "<td></td>";
                echo "<td>".$sumaCreditoMorosoDolares."</td>";

                echo "<td></td>";
                echo "<td>".$sumaLetraBancoDolares."</td>";
                echo "<td></td>";
                echo "<td>".$sumaLetraxFirmarDolares."</td>";
                echo "<td></td>";
                echo "<td>".$sumaLetraProtestadaDolares."</td>";
                echo "<td>total".$i."</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>".$sumaContadoCanceladoSoles."</td>";
                echo "<td></td>";
                echo "<td>".$sumaContadoPendienteSoles."</td>";
                echo "<td></td>";
                echo "<td>".$sumaContadoMorosoSoles."</td>";

                echo "<td></td>";
                echo "<td>".$sumaCreditoCanceladoSoles."</td>";
                echo "<td></td>";
                echo "<td>".$sumaCreditoPendienteSoles."</td>";
                echo "<td></td>";
                echo "<td>".$sumaCreditoMorosoSoles."</td>";

                echo "<td></td>";
                echo "<td>".$sumaLetraBancoSoles."</td>";
                echo "<td></td>";
                echo "<td>".$sumaLetraxFirmarSoles."</td>";
                echo "<td></td>";
                echo "<td>".$sumaLetraProtestadaSoles."</td>";
                echo "<td>total".$i."</td>";
            echo "</tr>";

            //suma de totales:
            //contado
            $totalContadoCanceladoDolares += $sumaContadoCanceladoDolares;
            $totalContadoPendienteDolares += $sumaContadoPendienteDolares;
            $totalContadoMorosoDolares += $sumaContadoMorosoDolares;
            $totalContadoCanceladoSoles += $sumaContadoCanceladoSoles;
            $totalContadoPendienteSoles += $sumaContadoPendienteSoles;
            $totalContadoMorosoSoles += $sumaContadoMorosoSoles;

            $totalCreditoCanceladoDolares += $sumaCreditoCanceladoDolares;
            $totalCreditoPendienteDolares += $sumaCreditoPendienteDolares;
            $totalCreditoMorosoDolares += $sumaCreditoMorosoDolares;
            $totalCreditoCanceladoSoles += $sumaCreditoCanceladoSoles;
            $totalCreditoPendienteSoles += $sumaCreditoPendienteSoles;
            $totalCreditoMorosoSoles += $sumaCreditoMorosoSoles;

            $totalLetraBancoDolares += $sumaLetraBancoDolares;
            $totalLetraxFirmarDolares += $sumaLetraxFirmarDolares;
            $totalLetraProtestadaDolares += $sumaLetraProtestadaDolares;
            $totalLetraBancoSoles += $sumaLetraBancoSoles;
            $totalLetraxFirmarSoles += $sumaLetraxFirmarSoles;
            $totalLetraProtestadaSoles += $sumaLetraProtestadaSoles;
        }
        //totales:
        echo "<tr>";
            echo "<td>TOTAL DÓLARES</td>";
            echo "<td></td>";
            echo "<td>".$totalContadoCanceladoDolares."</td>";
            echo "<td></td>";
            echo "<td>".$totalContadoPendienteDolares."</td>";
            echo "<td></td>";
            echo "<td>".$totalContadoMorosoDolares."</td>";

            echo "<td></td>";
            echo "<td>".$totalCreditoCanceladoDolares."</td>";
            echo "<td></td>";
            echo "<td>".$totalCreditoPendienteDolares."</td>";
            echo "<td></td>";
            echo "<td>".$totalCreditoMorosoDolares."</td>";

            echo "<td></td>";
            echo "<td>".$totalLetraBancoDolares."</td>";
            echo "<td></td>";
            echo "<td>".$totalLetraxFirmarDolares."</td>";
            echo "<td></td>";
            echo "<td>".$totalLetraProtestadaDolares."</td>";
            echo "<td>total".$i."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>TOTAL SOLES</td>";
            echo "<td></td>";
            echo "<td>".$totalContadoCanceladoSoles."</td>";
            echo "<td></td>";
            echo "<td>".$totalContadoPendienteSoles."</td>";
            echo "<td></td>";
            echo "<td>".$totalContadoMorosoSoles."</td>";

            echo "<td></td>";
            echo "<td>".$totalCreditoCanceladoSoles."</td>";
            echo "<td></td>";
            echo "<td>".$totalCreditoPendienteSoles."</td>";
            echo "<td></td>";
            echo "<td>".$totalCreditoMorosoSoles."</td>";

            echo "<td></td>";
            echo "<td>".$totalLetraBancoSoles."</td>";
            echo "<td></td>";
            echo "<td>".$totalLetraxFirmarSoles."</td>";
            echo "<td></td>";
            echo "<td>".$totalLetraProtestadaSoles."</td>";
            echo "<td>total".$i."</td>";
        echo "</tr>";
    }

    function reporteCuotaMensual(){
        $data['mes'] = $this->meses();
        $this->view->show('reporte/reporteCuotaMensual.phtml',$data);
    }

    function listaConsultaCuotaMensual(){
        $idvendedor = $_POST['idvendedor'];
        $moneda = $_POST['moneda'];
        $semana = $_POST['semana'];
        $mes = $_POST['mes'];
        $anio = $_POST['anio'];

        $reporte = new Reporte();

        $dataVendedor = $reporte->getNombreVendedor($idvendedor);
        $finicio = $anio."-".$mes."-01";
        $ffinal = $anio."-".$mes."-31";

        /*INICIO DE CABECERA*/
        echo '<h3 style="text-align:center;">'.$dataVendedor[0]['vendedor'].'</h3>';
        echo '<table id="tblCuotaMensual">
                <thead>
                    <tr>
                        <th rowspan="2">N°</th>
                        <th rowspan="2">ZONA</th>
                        <th rowspan="2">MAQUINARIAS</th>
                        <th rowspan="2">ELECTRONICA</th>
                        <th rowspan="2">AUTOPARTES</th>
                        <th rowspan="2">FERRETERIA<br>E ILUMINARIA</th>
                        <th rowspan="2">OTROS</th>
                        <th colspan="3">FORMA DE PAGO</th>
                        <th rowspan="2">TODOS</th>
                    </tr>
                    <tr>
                        <th>CONTADO</th>
                        <th>CREDITO</th>
                        <th>LETRAS</th>
                    </tr>
                </thead>
                <tbody>';
        /*FIN DE CABECERA*/
        $sumaMaquinaria = 0.0;
        $sumaElectronica = 0.0;
        $sumaAutopartes = 0.0;
        $sumaFerreteria = 0.0;
        $sumaOtros = 0.0;

        $sumaContado = 0.0;
        $sumaCredito = 0.0;
        $sumaLetras = 0.0;

        $data = $reporte->zonaVendedor($idvendedor,$moneda,$finicio,$ffinal);
        for($i=0;$i<count($data);$i++){
            echo '<tr>';
            echo '<td>'.($i+1).'</td>';
            echo '<td>'.$data[$i]['nombrezona'].'</td>';

            //inicialización de variables:
            $maquinarias = 0.0; //7
            $electronica = 0.0; //1
            $autopartes = 0.0; //5
            $ferreteria = 0.0; //8
            $otros = 0.0; //else
            $sumaVenta = 0.0;
            $sumaCobro = 0.0;
            $data1 = $reporte->listadoOrdenVentaCM($data[$i]['idzona'],$idvendedor,$moneda,$finicio,$ffinal);
            for($j=0;$j<count($data1);$j++){
                if($data1[$j]['idlinea'] == 7){
                    $maquinarias += $data1[$j]['total'];
                }else if($data1[$j]['idlinea'] == 1){
                    $electronica += $data1[$j]['total'];
                }else if($data1[$j]['idlinea'] == 5){
                    $autopartes += $data1[$j]['total'];
                }else if($data1[$j]['idlinea'] == 8){
                    $ferreteria += $data1[$j]['total'];
                }else{
                    $otros += $data1[$j]['total'];
                }
            }

            $sumaMaquinaria += $maquinarias;
            $sumaElectronica += $electronica;
            $sumaAutopartes += $autopartes;
            $sumaFerreteria += $ferreteria;
            $sumaOtros = $otros;

            echo '<td style="text-align:right;">'.number_format($maquinarias,2).'</td>';
            echo '<td style="text-align:right;">'.number_format($electronica,2).'</td>';
            echo '<td style="text-align:right;">'.number_format($autopartes,2).'</td>';
            echo '<td style="text-align:right;">'.number_format($ferreteria,2).'</td>';
            echo '<td style="text-align:right;">'.number_format($otros,2).'</td>';

            // forma de cobro:
            //variables iniciales:
            $contado = 0.0;
            $credito = 0.0;
            $letras = 0.0;

            $data2 = $reporte->listadoOrdenventa2($data[$i]['idzona'],$idvendedor,$moneda,$finicio,$ffinal);
            for($j=0;$j<count($data2);$j++){
                $data3 = $reporte->listadoOrdenCobro2($data2[$j]['idordenventa']);
                //var_dump($data3);
                for($x=0;$x<count($data3);$x++){
                    $data4 = $reporte->listadoDetOrdenCobro2($data3[$x]['idordencobro']);
                    //echo $data4;
                    for($y=0;$y<count($data4);$y++){
                        if($data4[$y]['formacobro'] == 1){
                            $contado += $data4[$y]['importedoc'];
                        }else if($data4[$y]['formacobro'] == 2){
                            $credito += $data4[$y]['importedoc'];
                        }else{
                            $letras += $data4[$y]['importedoc'];
                        }
                    }
                }
            }

            $sumaContado += $contado;
            $sumaCredito += $credito;
            $sumaLetras += $letras;

            echo '<td style="text-align:right;">'.number_format($contado,2).'</td>';
            echo '<td style="text-align:right;">'.number_format($credito,2).'</td>';
            echo '<td style="text-align:right;">'.number_format($letras,2).'</td>';

            //suma de totales
            $sumaVenta = $maquinarias+$electronica+$autopartes+$ferreteria+$otros;
            $sumaCobro = $contado + $credito + $letras;
            $totalv += $sumaVenta;
            $totalc += $sumaCobro;
            echo '<td style="text-align:right;">'.number_format($sumaVenta,2).'</td>';
            echo '</tr>';
        }

        echo '<tr>';
        echo '<td></td>';
        echo '<td>TOTAL</td>';
        echo '<td style="text-align:right;">'.number_format($sumaMaquinaria,2).'</td>';
        echo '<td style="text-align:right;">'.number_format($sumaElectronica,2).'</td>';
        echo '<td style="text-align:right;">'.number_format($sumaAutopartes,2).'</td>';
        echo '<td style="text-align:right;">'.number_format($sumaFerreteria,2).'</td>';
        echo '<td style="text-align:right;">'.number_format($sumaOtros,2).'</td>';
        echo '<td style="text-align:right;">'.number_format($sumaContado,2).'</td>';
        echo '<td style="text-align:right;">'.number_format($sumaCredito,2).'</td>';
        echo '<td style="text-align:right;">'.number_format($sumaLetras,2).'</td>';
        echo '<td style="text-align:right;">'.number_format($totalv,2).'</td>';
        echo '</tr>';

        echo '</tbody></table>';

    }

    function letrasxhacerplanilla2() {
        $this->view->show('/reporte/letrasxhacerplanilla2.phtml');
    }

    function reportedevolucion() {
        $zona = $this->AutoLoadModel('zona');
        $data['categoriaPrincipal'] = $zona->listaCategoriaPrincipal();
        $data['condicionVenta'] = $this->configIniTodo('TipoCobro');
        $this->view->show('/reporte/reportedevolucion.phtml', $data);
    }

    function detalladodeletras() {
        $zona = $this->AutoLoadModel('zona');
        $actor = $this->AutoLoadModel('actorrol');
        $tipoCobranza = $this->AutoLoadModel('tipocobranza');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['hijo'] = $zona->listacategoriaHijo();
        $data['zona'] = $zona->listadoTotalZona();
        $this->view->show('/reporte/detalladodeletras.phtml', $data);
    }
    
    function reportecobranzageneral() {
        $this->view->show('/reporte/reportecobranzageneral.phtml');
    }    
    
    function resumencobranzapagado() {
        $zona = $this->AutoLoadModel('zona');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['hijo'] = $zona->listacategoriaHijo();
        $data['zona'] = $zona->listadoTotalZona();
        $this->view->show('/reporte/resumencobranzapagado.phtml', $data);
    }

    function letrasxhacerplanilla3() {
        $this->view->show('/reporte/letrasxhacerplanilla3.phtml');
    }
    function devolucionesconta() {
        $this->view->show('/reporte/reportedevolucionconta.phtml');
    }

    function ventasfacturadonofacturado() {
        $this->view->show('/reporte/reporteventasfacturadonofacturado.phtml');
    }
    function formatoInventario() {
        $inventario = $this->AutoLoadModel('inventario');
        $data['inventario'] = $inventario->listadoConFecha();
        $bloques = $this->AutoLoadModel('bloques');
        $data['bloques'] = $bloques->listado();
        $this->view->show('/reporte/formatoInventario.phtml', $data);
    }
    
    function devolucionesparacontaresumidito() {
        $this->view->show('/reporte/reportedevolucioncontaresumidito.phtml');
    }
    function rankingclientesseguncontabilidad() {
        $this->view->show('/reporte/rankingclienteseguncontabilidad.phtml');
    }
    
    function reposiciondeitems() {
        $this->view->show('/reporte/reposiciondeitems.phtml');
    }
    
    function reposiciondeitems_consultar() {
        set_time_limit(1800);
        $ordenCompra = $this->AutoLoadModel('ordencompra');
        $idproducto = $_REQUEST['idProducto'];
        $reporte = $this->AutoLoadModel('reporte');
        $soloCompras = $reporte->reporteKardexProduccionDetallado("", "", $idproducto, 1, "", "m.idordencompra!=''");
        $tamSoloCompras = count($soloCompras);
        $fechaInicioMovimiento = '';
        for ($sci = 0; $sci < $tamSoloCompras; $sci++) {
            $fechaInicioMovimiento = $soloCompras[0]['fecha'];
            $dataCompra[$sci]['idordencompra'] = $soloCompras[$sci]['idordencompra'];
            $dataCompra[$sci]['codigooc'] = $soloCompras[$sci]['codigooc'];
            $dataCompra[$sci]['fcompra'] = $soloCompras[$sci]['fecha'];
            $dataCompra[$sci]['fultimaagotado'] = '';
            $dataCompra[$sci]['fultimaventa'] = '';
            $dataCompra[$sci]['cantidad'] = $soloCompras[$sci]['cantidad'];
            $dataCompra[$sci]['cantidadvendida'] = 0;
//            echo $soloCompras[$sci]['codigooc'] . " | ";
        }
        $auxCompra = 0;
        $tempCantidad = 0;
        $productos = $reporte->reporteKardexProduccionDetallado2($idproducto, $fechaInicioMovimiento);

//        echo " <b>CANT. PRODUCTOS:</b> " . count($productos) . " <br><br>";
        $totalventaproducto = 0;
        $totaldevoluciones = 0;
        echo "<table>";
        for ($y = 0; $y < count($productos) && isset($dataCompra[$auxCompra]['codigooc']); $y++) {
//            echo "<tr>";
//            echo "<td>" . $y . "</td>";
//            echo "<td>" . $productos[$y]['codigov'] . "</td>";
//            echo "<td>" . $productos[$y]['fecha'] . "</td>";
//            echo "<td>CONCEPTO " . $productos[$y]['conceptomovimiento'] . "</td>";
            if (empty($productos[$y]['cantidaddevolucion'])) {
                $productos[$y]['cantidaddevolucion'] = 0;
            }
            if ($productos[$y]['conceptomovimiento'] == "01") {
                $totaldevoluciones += $productos[$y]['cantidaddevolucion'];
                $totalventaproducto += $productos[$y]['cantidad'];
                $tempCantidad += $productos[$y]['cantidad'] - $productos[$y]['cantidaddevolucion'];
                $dataCompra[$auxCompra]['fultimaventa'] = $productos[$y]['fecha'];
//                echo "<td>" . "</td>";
            } else {
//                echo "<td>MOVIMIENTO " . $productos[$y]['tipomovimiento'] . "</td>";
                if ($productos[$y]['tipomovimiento'] == 1) {
                    $totaldevoluciones += $productos[$y]['cantidaddevolucion'];
                    $totalventaproducto += $productos[$y]['cantidad'];
                    $tempCantidad -= $productos[$y]['cantidad'];
                } else {
                    $tempCantidad += $productos[$y]['cantidad'];
                }
            }            
//            echo "<td>" . $productos[$y]['cantidad'] . "</td>";
//            echo "<td>" . $productos[$y]['cantidaddevolucion'] . "</td>";
//            echo "<td>TMP CANTIDAD: " . $tempCantidad . "</td>";
            if ($tempCantidad <= $dataCompra[$auxCompra]['cantidad']) {
                $dataCompra[$auxCompra]['cantidadvendida'] = $tempCantidad;
            } else {
                $dataCompra[$auxCompra]['cantidadvendida'] = $dataCompra[$auxCompra]['cantidad'];
            }
//            echo "<td>Para " . $dataCompra[$auxCompra]['cantidad'] . "</td>";
//            echo "</tr>";
            if (isset($dataCompra[$auxCompra]['codigooc']) && $tempCantidad >= $dataCompra[$auxCompra]['cantidad']) {
                $dataCompra[$auxCompra]['fultimaagotado'] = $productos[$y]['fecha'];
//                echo "<tr>";
//                echo "<th colspan='2'>Antes " . $tempCantidad . "</th>";
//                echo "<th colspan='2'>" . $dataCompra[$auxCompra]['cantidad'] . "</th>";
//                echo "<th colspan='2'>REINICIO TMP CANTIDAD " . $tempCantidad . "</th>";
//                echo "<th colspan='2'>COMPRA: " . $dataCompra[$auxCompra]['codigooc'] . "</th>";                
                $dataCompra[$auxCompra]['fultimaagotado'] = $productos[$y]['fecha'];
                $tempCantidad = $tempCantidad - $dataCompra[$auxCompra]['cantidad'];
                //$dataCompra[$auxCompra]['cantidadvendida'] -= $tempCantidad;
                $auxCompra++;
                if (isset($dataCompra[$auxCompra]['codigooc'])) {
                    $dataCompra[$auxCompra]['cantidadvendida'] = $tempCantidad;
                }
//                echo "</tr>";
            }
        }
        echo "</table>";
        $producto = $this->AutoLoadModel('producto');
        $dataProducto = $producto->buscaProducto($idproducto);        
        if (count($dataProducto) > 0) {
//            echo '<br><br>ACTUAL: ' . $dataProducto[0]['stockactual'] . ' DISPONIBLE: ' .$dataProducto[0]['stockdisponible'] .
//                        '<br><br>';
            if ($auxCompra > 0) {
                $auxCompra--;
            }
            $stockfaltante = 0;
            for ($sci = $auxCompra; $sci < $tamSoloCompras; $sci++) {
                $stockfaltante += $dataCompra[$sci]['cantidad'] - $dataCompra[$sci]['cantidadvendida'];
            }
//            echo " =>FALTANTE: " . $stockfaltante . " =< STOCK: " . $dataProducto[0]['stockactual'] . "<br>";
            if ($stockfaltante > $dataProducto[0]['stockactual']) {
                $stockfaltante = $stockfaltante - $dataProducto[0]['stockactual'];
                for ($sci = $auxCompra; $sci < $tamSoloCompras && $stockfaltante > 0; $sci++) {
                    if ($dataCompra[$sci]['cantidadvendida'] != $dataCompra[$sci]['cantidad']) {
                        $dataCompra[$sci]['cantidadvendida'] += $stockfaltante;                        
                        if ($dataCompra[$sci]['cantidadvendida'] >= $dataCompra[$sci]['cantidad']) {
                            $stockfaltante = $dataCompra[$sci]['cantidadvendida'] - $dataCompra[$sci]['cantidad'];
                            $dataCompra[$sci]['cantidadvendida'] = $dataCompra[$sci]['cantidad'];
                            if (!empty($dataCompra[$sci]['fultimaventa'])) {
                                $dataCompra[$sci]['fultimaagotado'] = $dataCompra[$sci]['fultimaventa'];
                            }
                        } else {
                            $stockfaltante = 0;
                        }
                    }                    
                }
            } /*else if ($stockfaltante < $dataProducto[0]['stockactual']) {
                $stockfaltante = $stockfaltante - $dataProducto[0]['stockactual'];
                echo "ESTO FALTA: " . $stockfaltante;
            }*/
            echo '<table>
                <tbody>
                    <tr>
                        <th colspan="4">REPORTE ESTADISTICO DE REPOSICION DE ITEMS</th>
                    </tr>
                    <tr>
                        <th>Codigo:</th>
                        <td style="color:blue">' . $dataProducto[0]['codigopa'] . '</td>
                        <th>Descripcion:</th>
                        <td style="color:blue">' . html_entity_decode(trim($dataProducto[0]['nompro']), ENT_QUOTES, 'UTF-8') . '</td>
                    </tr>
                </tbody>
            </table>';
        }
        if ($tamSoloCompras > 0 && count($dataProducto) > 0) {
            $detalleOrdenCompra = $this->AutoLoadModel('detalleordencompra');
            
            $data['porcifventas'] = $this->configIni('Parametros', 'PorCifVentas');
            $porcentaje = (($data['porcifventas'] + 100) / 100);
            
            
            $cantidad = count($dataCompra);
            for ($z = $cantidad - 1; $z >= 0; $z--) {
                $porcentaje = (($data['porcifventas'] + 100) / 100);
                $cifventacpa_ordencompra = $ordenCompra->solicitarCifventascpa($dataCompra[$z]['codigooc']);
                if ($cifventacpa_ordencompra > 0) {
                    $porcentaje = (($cifventacpa_ordencompra + 100) / 100);
                }
                echo '<table>
                        <tbody>
                            <tr>
                                <th colspan="3" style="color:black;background:#4096EE;">ORDEN COMPRA:</th>
                                <td colspan="2">' . $dataCompra[$z]['codigooc'] . '</td>
                                <th style="color:black;background:#4096EE;">FECHA COMPRA:</th>
                                <td colspan="3">' . $dataCompra[$z]['fcompra'] . '</td>
                            </tr>
                            <tr>
                                <th>QTY</th>
                                <th>FOB</th>
                                <th>PV</th>
                                <th>%</th>
                                <th>STOCK A LA FECHA</th>
                                <th>FECHA AGOTADO</th>
                                <th>TIEMPO DE AGOTAMIENTO</th>
                                <th>ULT. FECHA VENTA</th>
                                <th>TIEMPO DE VENTA:</th>
                            </tr>';
//                echo "Ordencompra: " . $dataCompra[$z]['idordencompra'] . "<br>";
//                echo "Codigo: " . $dataCompra[$z]['codigooc'] . "<br>";
//                echo "Cantidad: " . $dataCompra[$z]['cantidad'] . "<br>";
//                echo "Cantidad Vendida: " . $dataCompra[$z]['cantidadvendida'] . "<br>";
//                echo "Fecha Compra: " . $dataCompra[$z]['fcompra'] . "<br>";
//                echo "Vendido: " . $dataCompra[$z]['fultimaagotado'] . "<br>";
                /* if (!empty($dataCompra[$z]['fultimaagotado'])) {
                  echo "Tiempo de Ventas: " . ($detalleOrdenCompra->cantidad_dias_entre_dos_fechas($dataCompra[$z]['fcompra'], $dataCompra[$z]['fultimaagotado'])) . " dias<br>";
                  } */
                $precioVenta = 0;
                $fob = 0;
                $utilidadReal = 0;
                $dataDetalleordenCompra = $detalleOrdenCompra->listaDetalleOrdenCompraxproducto($dataCompra[$z]['idordencompra'], $idproducto);
                if (count($dataDetalleordenCompra) > 0) {
                    if ($dataDetalleordenCompra[0]['precio_listadolares'] > 0) {
                        $preciolistaDolares = $dataDetalleordenCompra[0]['precio_listadolares'];
                    } else {
                        $preciolistaDolares = $dataDetalleordenCompra[0]['preciolista'] / $dataDetalleordenCompra[0]['tipocambiovigenteoc'];
                    }
                    //$utilidadporcentaje=($dataDetalleordenCompra[$i][preciotopedolares]-$cifventas)*100/$cifventas;
                    $descuento13 = $preciolistaDolares - ($preciolistaDolares * 0.13);
                    $descuento5 = $descuento13 - ($descuento13 * 0.05);
                    $descuento95 = $descuento5 - ($descuento5 * 0.095);
                    $precioVenta = $descuento95 - ($descuento95 * 0.05);
                    $fob = $dataDetalleordenCompra[0]['fobdoc'];
                    $cifventas = $dataDetalleordenCompra[0]['fobdoc'] * $porcentaje;
                    $utilidadReal = (($precioVenta - $cifventas) / $cifventas) * 100;
                }
                echo '<tr>
                        <td>' . $dataCompra[$z]['cantidad'] . '</td>
                        <td>US $ ' . number_format($fob, 2) . '</td>
                        <td>US $ ' . number_format($precioVenta, 2) . '</td>
                        <td>' . number_format($utilidadReal, 1) . '%</td>
                        <td>' . ($dataCompra[$z]['cantidad'] - $dataCompra[$z]['cantidadvendida']). '</td>
                        <td>' . (!empty($dataCompra[$z]['fultimaagotado']) ? $dataCompra[$z]['fultimaagotado'] : '-') . '</td>
                        <td>' . (!empty($dataCompra[$z]['fultimaagotado']) ? ($detalleOrdenCompra->cantidad_dias_entre_dos_fechas($dataCompra[$z]['fcompra'], $dataCompra[$z]['fultimaagotado'])) . ' dias' : '-') . '</td>
                        <td>' . (!empty($dataCompra[$z]['fultimaventa']) ? $dataCompra[$z]['fultimaventa'] : '-') . '</td>
                        <td>' . (!empty($dataCompra[$z]['fultimaventa']) ? ($detalleOrdenCompra->cantidad_dias_entre_dos_fechas($dataCompra[$z]['fcompra'], $dataCompra[$z]['fultimaventa'])) . ' dias' : '-') . '</td>
                    </tr>
                </tbody>
            </table>';
//                echo "Fob: " . number_format($fob, 2) . "<br>";
//                echo "Precio Venta: " . number_format($precioVenta, 2) . "<br>";
//                echo "%:" . number_format($utilidadReal, 1) . "%<br>";
//                echo "<br><hr><hr>";
            }
            //echo $tempCantidad;
        }
    }
      function reporteProductosBloque() {
        $inventario = $this->AutoLoadModel('inventario');
        $data['inventario'] = $inventario->listadoConFecha();
        $bloques = $this->AutoLoadModel('bloques');
        $data['bloques'] = $bloques->listado();
        $this->view->show('/reporte/reporteProductosBloque.phtml', $data);
    }

    function saldosiniciales1() {
        $this->view->show('/reporte/reportesaldosiniciales1.phtml');
    }

 function vencidasvendedor() {
        $zona = $this->AutoLoadModel('zona');
        $actor=$this->AutoLoadModel('actorrol');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['hijo'] = $zona->listacategoriaHijo();
        $data['zona'] = $zona->listadoTotalZona();

        $data['vendedor']=$actor->actoresxRolxNombre(25);
        $this->view->show('/reporte/reporteVentasvencidosvendedor.phtml',$data);
    }

    function reporteletraszona() {
        set_time_limit(1000);
        // Crearción de instacias de reporte, tipocobranza, ordengasto, tipocobro y movimiento:
        $reporte = $this->AutoLoadModel('reporte');
        $tipo = $this->AutoLoadModel('tipocobranza');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $tipoCobroIni = $this->configIniTodo('TipoCobro');
        $movimiento = $this->AutoLoadModel('movimiento');
        // recepción de variables:
        $idzona = $_REQUEST['idzona'];
        $idcategoriaprincipal = $_REQUEST['idcategoriaprincipal'];
        $idcategoria = $_REQUEST['idcategoria'];
        $idvendedor = $_REQUEST['idvendedor'];
        $idtipocobranza = $_REQUEST['idtipocobranza'];
        $idtipocobro = $_REQUEST['idtipocobro'];
        $fechaInicio = $_REQUEST['fechaInicio'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        $pendiente = $_REQUEST['pendiente'];
        $cancelado = $_REQUEST['cancelado'];
        $octava = $_REQUEST['octava'];
        $novena = $_REQUEST['novena'];
        $idcobrador = $_REQUEST['idcobrador'];
        $IdCliente = $_REQUEST['IdCliente'];
        $IdOrdenVenta = $_REQUEST['IdOrdenVenta'];
        $recepcionLetras = $_REQUEST['recepcionLetras'];
        $orderDireccion = $_REQUEST['orderDireccion'];


        $octavaNovena = " ";
        if (!empty($octava) && !empty($novena)) {
            $octavaNovena.=" and (wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 8 DAY) or wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 9 DAY)) and wc_detalleordencobro.`situacion`='' ";
        } elseif (!empty($novena)) {

            $octavaNovena.=" and wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 9 DAY) and wc_detalleordencobro.`situacion`='' ";
        } elseif (!empty($octava)) {
            $octavaNovena.=" and wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 8 DAY) and wc_detalleordencobro.`situacion`='' ";
        }

        $situacion = "";
        if (!empty($pendiente) && !empty($cancelado)) {
            $situacion.=" and (wc_detalleordencobro.`situacion`='' or wc_detalleordencobro.`situacion`='cancelado') ";
        } elseif (!empty($cancelado)) {
            $situacion.=" and wc_detalleordencobro.`situacion`='cancelado' ";
        } elseif (!empty($pendiente)) {
            $situacion.=" and wc_detalleordencobro.`situacion`='' ";
        }
        if ($_REQUEST['fechaInicio'] != "") {
            $fechaInicio = date('Y-m-d', strtotime($_REQUEST['fechaInicio']));
        }
        $fechaFinal = $_REQUEST['fechaFinal'];
        if ($_REQUEST['fechaFinal'] != "") {
            $fechaFinal = date('Y-m-d', strtotime($_REQUEST['fechaFinal']));
        }
        if ($_REQUEST['fechaPagoInicio'] != "") {
            $fechaPagoInicio = date('Y-m-d', strtotime($_REQUEST['fechaPagoInicio']));
        } else {
            $fechaPagoInicio = $_REQUEST['fechaPagoInicio'];
        }
        if ($_REQUEST['fechaPagoFinal'] != "") {
            $fechaPagoFinal = date('Y-m-d', strtotime($_REQUEST['fechaPagoFinal']));
        } else {
            $fechaPagoFinal = $_REQUEST['fechaPagoFinal'];
        }
        $idcategorias = "";
        if (!empty($idcobrador)) {
            $cobrador = $this->AutoLoadModel('cobrador');
            $dataCobrador = $cobrador->buscaZonasxCobrador($idcobrador);
            $cantidadCobrador = count($dataCobrador);
            if ($cantidadCobrador != 0) {
                $idcategorias.=" and (";
                for ($i = 0; $i < $cantidadCobrador; $i++) {
                    if ($i == 0) {
                        $idcategorias.=" wc_categoria.`idcategoria`='" . $dataCobrador[$i]['idzona'] . "' ";
                    } else {
                        $idcategorias.=" or wc_categoria.`idcategoria`='" . $dataCobrador[$i]['idzona'] . "' ";
                    }
                }
                $idcategorias.=" ) ";
            } else {
                $idcategorias.=" and  wc_categoria.`idcategoria`='0' ";
            }
        } elseif (!empty($idcategoria)) {
            $idcategorias = " and wc_categoria.`idcategoria`='" . $idcategoria . "' ";
        }
        if ($idtipocobro == 3) {//letras al banco
            $filtro = "wc_detalleordencobro.`formacobro`='3' and wc_ordencobro.`tipoletra`=1";
//            if(!empty($recepcionLetras)){
//                if($recepcionLetras == 1){
//                    $filtro.="and wc_detalleordencobro.`recepcionLetras`='PA'";
//                }else{
//                    $filtro.="and wc_detalleordencobro.`recepcionLetras`=''";
//                }
//            }
        } elseif ($idtipocobro == 4) {//letras cartera
            $filtro = "wc_detalleordencobro.`formacobro`='3' and  wc_ordencobro.`tipoletra`=2";
        } elseif ($idtipocobro == 2) {//credito
            $filtro = "wc_detalleordencobro.`formacobro`='2' and wc_detalleordencobro.referencia=''";
        } elseif ($idtipocobro == 1) {//al contado
            $filtro = "wc_detalleordencobro.`formacobro`='1' ";
        } elseif ($idtipocobro == 5) {//letras protestadas
            $filtro = "wc_detalleordencobro.`formacobro`='2' and (substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p')";
            $dias = 10;
        }

        $totalPagado = 0;
        $totalImporte = 0;
        $importe = 0;
        $totalDevolucion = 0;
        $total = 0;
        $TPagado = 0;
        $cont = 0;
        $fechaActual = date('Y-m-d');
        $datareporte = $reporte->reportletraszona($filtro, $idzona, $idcategoriaprincipal, $idcategorias, $idvendedor, $idtipocobranza, $fechaInicio, $fechaFinal, $octavaNovena, $situacion, $fechaPagoInicio, $fechaPagoFinal, $IdCliente, $IdOrdenVenta,$orderDireccion);
//
        $dataAnterior = $datareporte[-1]['idordenventa'];


        echo "<thead>
                    <tr>
                            <th >Codigo</th>
                            <th class='ocultarImpresion'>Vendedor</th>
                            <th class='mostrarImpresion' style='display:none'>Ven</th>
                            <th class='ocultarImpresion'>Zona Cobranza</th>
                            <th class='ocultarImpresion'>Zona </th>
                            <th>F. Des.".$orderDireccion."</th>
                            <th>F. venc.</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Pagado</th>
                            <th>Devol.</th>
                            <th>Deuda</th>
                            <th class='ocultarImpresion'>Tipo Cobranza</th>
                            <th>" . date('d/m') . "</th>
                            <th>" . date('d/m', strtotime("$fechaActual + 1 day")) . "</th>
                            <th>" . date('d/m', strtotime("$fechaActual + 2 day")) . "</th>
                            <th>" . date('d/m', strtotime("$fechaActual + 3 day")) . "</th>
                            <th>" . date('d/m', strtotime("$fechaActual + 4 day")) . "</th>

                    </tr>
                    <tr class='ocultarImpresion'><td colspan='10'>&nbsp;</td></tr>
              </thead>
              <tbody>";

        $cantidadreporte = count($datareporte);

//        echo "<tr><td>";

//        echo "--------------------------------------------------------".$recepcionLetras."<br>";
        //var_dump($datareporte);

//        echo "--------------------------------------------------------<br>";

        if ($idtipocobro == 3 && !empty($recepcionLetras)){

            $auxDatareporte = array();

//            echo "recepion::".$recepcionLetras."<br>";

            if($recepcionLetras == 1){
                $comp = 'PA';
            }else if($recepcionLetras == 2){
                $comp = '';
            }
            $auxcont = 0;
            for($i = 0; $i< $cantidadreporte;$i++){
                if ($dataAnterior != $datareporte[$i]['idordenventa']) {
                    $dataAnterior = $datareporte[$i]['idordenventa'];
                }
//                echo $datareporte[$i]['recepcionletras']."==".$comp."??<br>";
                if($datareporte[$i]['recepcionletras'] == $comp){
                    $auxDatareporte[$auxcont] = $datareporte[$i];
                    $auxcont++;
                }
            }
            $datareporte = $auxDatareporte;
        }

//        echo "--------------------------------------------------------: ".$auxcont."<br>";
//        var_dump($auxDatareporte);
//        echo "--------------------------------------------------------<br>";
//        var_dump($datareporte);
//        echo "--------------------------------------------------------<br>";
//        echo "</td></tr>";
        
        $axuZona = -1;

        for ($i = 0; $i < $cantidadreporte; $i++) {
            if ($axuZona != $datareporte[$i]['idzona']){
                if($axuZona != -1) {
                    echo "<tr><td></td></tr>";
                }
                $axuZona = $datareporte[$i]['idzona'];
                if ($i > 0) {
                    echo "<tr style='font-weight:bold;border-radius:10px;background-color:rgb(124, 180, 224)'>" . 
                            "<th rowspan='2' style='text-align:center;'>TOTAL ZONA<br>" . $datareporte[$i-1]['nombrezona'] . "</th>" . 
                            
                            "<td colspan='2' style='text-align:right;'>Total:</td>" . 
                            "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['S/']['totalImporte'], 2) . "</th>" . 
                            "<td colspan='2' style='text-align:right;'>Total Pagado:</td>" . 
                            "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['S/']['TPagado'], 2) . "</th>" . 
                            "<td colspan='2' style='text-align:right;'>Total Devolucion:</td>" . 
                            "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['S/']['totalDevolucion'], 2) . "</th>" . 
                            "<td colspan='2' style='text-align:right;'>Total Deuda:</td>" .
                            "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['S/']['totalDeuda']-$acumulaxIdMoneda_temporal['S/']['totalDevolucion'], 2) . "</th>" . 
                          "</tr>
                          <tr style='font-weight:bold;border-radius:10px;background-color:rgb(124, 180, 224)'>" . 
                            "<td colspan='2' style='text-align:right;'>Total:</td>" . 
                            "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['US $']['totalImporte'], 2) . "</th>" . 
                            "<td colspan='2' style='text-align:right;'>Total Pagado:</td>" . 
                            "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['US $']['TPagado'], 2) . "</th>" . 
                            "<td colspan='2' style='text-align:right;'>Total Devolucion:</td>" . 
                            "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['US $']['totalDevolucion'], 2) . "</th>" . 
                            "<td colspan='2' style='text-align:right;'>Total Deuda:</td>" .
                            "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['US $']['totalDeuda']-$acumulaxIdMoneda_temporal['US $']['totalDevolucion'], 2) . "</th>" . 
                          "</tr>";
                }
                $acumulaxIdMoneda_temporal['S/']['totalImporte'] = 0;
                $acumulaxIdMoneda_temporal['S/']['TPagado'] = 0;
                $acumulaxIdMoneda_temporal['S/']['totalDevolucion'] = 0;
                $acumulaxIdMoneda_temporal['US $']['totalImporte'] = 0;
                $acumulaxIdMoneda_temporal['US $']['TPagado'] = 0;
                $acumulaxIdMoneda_temporal['US $']['totalDevolucion'] = 0;
                echo "<tr>
                        <th style='padding: 10px;text-align: center;border:1px solid black;color: black;background-color:white;font-size: 15px;font-weight: bold;' colspan='3'>ZONA: </th>
                        <td style='padding: 10px;color: rgb(153,0,0);border:1px solid black; background-color:white;font-size: 15px;font-weight: bold;' colspan='14'>" . $datareporte[$i]['nombrezona'] . "</td>
                    </tr>";                
            }
            
            if (!empty($dias)) {
                $datareporte[$i]['diffechas'] = $datareporte[$i]['diffechas'] + 10;
            }
            $simbolomoneda = $datareporte[$i]['simbolo'];
            if (strcasecmp($datareporte[$i]['situacion'], '') == 0) {
                $color = "style='color:red;text-align:right;'";
                $total+=$datareporte[$i]['saldodoc'];
            } else {
                $color = "style='color:blue;text-align:right;'";
                $totalPagado+=$datareporte[$i]['importedoc'] - $datareporte[$i]['saldodoc'];
            }
            if ($dataAnterior != $datareporte[$i]['idordenventa']) {

                $dataAnterior = $datareporte[$i]['idordenventa'];
                $dataTipoCobranza = $tipo->buscaxid($datareporte[$i]['idtipocobranza']);
                $tipocobranza = !empty($dataTipoCobranza[0]['nombre']) ? $dataTipoCobranza[0]['nombre'] : 'Sin Asignar';
                $importe = $ordenGasto->totalGuia($datareporte[$i]['idordenventa']);
                $percepcion = $ordenGasto->ImporteGastoxIdDetalleOrdenCobro($datareporte[$i]['iddetalleordencobro']);
                $acumulaxIdMoneda[$simbolomoneda]['totalImporte']+=$importe;
                $acumulaxIdMoneda[$simbolomoneda]['TPagado']+=$datareporte[$i]['importepagado'];
                $acumulaxIdMoneda[$simbolomoneda]['totalDevolucion']+=$datareporte[$i]['importedevolucion'];
                $acumulaxIdMoneda[$simbolomoneda]['totalDeuda'] = $acumulaxIdMoneda[$simbolomoneda]['totalImporte'] - $acumulaxIdMoneda[$simbolomoneda]['TPagado'];
                
                $acumulaxIdMoneda_temporal[$simbolomoneda]['totalImporte']+=$importe;
                $acumulaxIdMoneda_temporal[$simbolomoneda]['TPagado']+=$datareporte[$i]['importepagado'];
                $acumulaxIdMoneda_temporal[$simbolomoneda]['totalDevolucion']+=$datareporte[$i]['importedevolucion'];
                $acumulaxIdMoneda_temporal[$simbolomoneda]['totalDeuda'] = $acumulaxIdMoneda_temporal[$simbolomoneda]['totalImporte'] - $acumulaxIdMoneda_temporal[$simbolomoneda]['TPagado'];

                echo "<tr style='border-radius:10px;background-color:rgb(124, 180, 224)'>
                                                 <td style='width:18mm'>" . $datareporte[$i]['codigov'] . "</td>
                                                 <td class='ocultarImpresion'>" . substr($datareporte[$i]['codigoa'] . ' ' . $datareporte[$i]['apellidopaterno'] . ' ' . $datareporte[$i]['apellidomaterno'] . ' ' . $datareporte[$i]['nombres'], 0, 20) . "</td>
                                                 <td class='mostrarImpresion' style='display:none'>" . $datareporte[$i]['codigoa'] . "</td>
                                                 <td class='ocultarImpresion'>" . $datareporte[$i]['nombrec'] . "</td>
                                                 <td class='ocultarImpresion'>" . $datareporte[$i]['nombrezona'] . "</td>
                                                 <td>" . date('d/m/y', strtotime($datareporte[$i]['fechadespacho'])) . "</td>
                                                 <td>" . date('d/m/y', strtotime($datareporte[$i]['fechavencimiento'])) . "</td>
                                                 <td style='width:36mm'>" . $datareporte[$i]['razonsocial'] . "</td>
                                                 <td>" . $simbolomoneda . " " . number_format($importe, 2) . "</td>
                                                 <td>" . $simbolomoneda . " " . number_format($datareporte[$i]['importepagado'], 2) . "</td>
                                                 <td>" . $simbolomoneda . " " . number_format($datareporte[$i]['importedevolucion'], 2) . "</td>
                                                 <td>" . $simbolomoneda . " " . number_format($importe - $datareporte[$i]['importepagado'] - $datareporte[$i]['importedevolucion'], 2) . "</td>
                                                 <td class='ocultarImpresion'>" . $tipocobranza . "</td>
                                                 <td style='width:15mm;border:1px solid;'>&nbsp;</td>
                                                <td style='width:15mm;border:1px solid;'>&nbsp;</td>
                                                <td style='width:15mm;border:1px solid;'>&nbsp;</td>
                                                <td style='width:15mm;border:1px solid;'>&nbsp;</td>
                                                <td style='width:15mm;border:1px solid;'>&nbsp;</td>
                                        </tr>";
                echo "<tr  class='filaContenedor' style='padding-left:0px ;border:solid 1px;'>
                                                <td colspan='18'>
                                                        <table class='filaOculta' style='display:none;margin:0px'><tr><td colspan='15'><a class='ver' href='#'>&nbsp<img src='/imagenes/iconos/OrdenAbajo.gif'></a></td></tr></table>
                                                        <table class='tblchildren' style='margin:0px;padding:0px;'>
                                                                <thead>
                                                                        <tr class='ocultarImpresion'>
                                                                                <th style='width:70mm'>Direccion</th>

                                                                                <th style='width:30mm'>Estado</th>
                                                                                <th style='width:15mm'>Cond. Venta</th>
                                                                                <th style='width:10mm'>N° Letra</th>
                                                                                <th style='width:15mm'>F. Girooo</th>
                                                                                <th style='width:15mm'>F. Ven.</th>
                                                                                <th style='width:15mm'>F. Can.</th>
                                                                                <th>N° Unico</th>
                                                                                <th>Indicador</th>
                                                                                <th>Imp.<br>Letra</th>
                                                                                <th>Gasto<br>Protesto</th>
                                                                                <th>Importe</th>                                                                                
                                                                                <th>Percepcion</th>
                                                                                <th>Saldo</th>
                                                                                <th>Situacion</th>
                                                                                <th style='width:25mm'>Referencia <a class='ocultar' style='margin-left:0px;' href='#'><img src='/imagenes/iconos/OrdenArriba.gif'></a></th>
                                                                        </tr>
                                                                </thead>
                                                                <tbody>";
            }
            echo "<tr style='margin-botton:none;'>";
            if ($cont == 0) {
                echo "<td >" . $datareporte[$i]['direccion']."</td>";


                $cont++;
            }else {
                echo "<td >&nbsp;</td>";
            }
            $temImporteLettra = '';
            $temImporteProtesto = '';
            if ($datareporte[$i]['formacobro'] == 2) {
                $tempReferencia = $datareporte[$i]['referencia'];
                $ultimaLetra = $tempReferencia[strlen($datareporte[$i]['referencia']) - 1];
                if (($ultimaLetra == 'p' || $ultimaLetra == 'P')) {
                    $temImporteLettra = $simbolomoneda . " " . number_format($datareporte[$i]['importedoc'] - $datareporte[$i]['montoprotesto'], 2);
                    $temImporteProtesto = $simbolomoneda . " " . number_format($datareporte[$i]['montoprotesto'], 2);
                }
            }
            echo "
                    <td ><h4><strong>" . ($dias == 10 ? 'PROTESTO - ' : "") . "</strong>" . ($datareporte[$i]['idtipocobranza'] == 4 ? 'INCOBRABLES' : strtoupper($tipo->NombreTipoCobranzaxDiasVencidos($datareporte[$i]['diffechas']))) . "</h4></td>
                    <td style='text-align:center'>" . $tipoCobroIni[$datareporte[$i]['formacobro']] . "</td>
                    <td >" . ($datareporte[$i]['numeroletra']) . "</td>
                    <td >" . date('d/m/y', strtotime($datareporte[$i]['fechagiro'])) . "</td>
                    <td >" . date('d/m/y', strtotime($datareporte[$i]['fvencimiento'])) . "</td>
                    <td >" . $this->FechaFormatoCorto($datareporte[$i]['fechapago']) . "</td>
                    <td >" . $datareporte[$i]['numerounico'] . "</td>
                    <td >" . $datareporte[$i]['recepcionletras'] . "</td>
                    <td >" . $temImporteLettra . "</td> 
                    <td >" . $temImporteProtesto . "</td>
                    <td >" . $simbolomoneda . " " . number_format($datareporte[$i]['importedoc'], 2) . "</td>
                    
                    <td >" . (!empty($percepcion) ? ($simbolomoneda . " " . number_format($percepcion, 2)) : '') . "</td>
                    <td >" . $simbolomoneda . " " . number_format($datareporte[$i]['saldodoc'], 2) . "</td>
                    <td >" . ($datareporte[$i]['situacion'] == '' ? 'Pendiente' : $datareporte[$i]['situacion']) . "</td>
                    <td >" . strtoupper($datareporte[$i]['proviene'] . " " . substr($datareporte[$i]['referencia'], 0, 11)) . "</td>
            </tr>";
            if ($dataAnterior != $datareporte[$i + 1]['idordenventa']) {
                $cont = 0;
                echo "<tr> <th colspan='1'>Telefono / Celular: </th> <td colspan='7'>" . $datareporte[$i]['telefono']."</td> <th colspan='2'>Atiende: </th> <td colspan='6'>" . $datareporte[$i]['contacto']."</td> </tr>"."</tbody>
                                                        </table>
                                                </td>

                                        </tr>";
            }
        }
        if ($i > 0) {
            echo "<tr style='font-weight:bold;border-radius:10px;background-color:rgb(124, 180, 224)'>" . 
                    "<th rowspan='2' style='text-align:center;'>TOTAL ZONA<br>" . $datareporte[$i-1]['nombrezona'] . "</th>" . 
                    "<td colspan='2' style='text-align:right;'>Total:</td>" . 
                    "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['S/']['totalImporte'], 2) . "</th>" . 
                    "<td colspan='2' style='text-align:right;'>Total Pagado:</td>" . 
                    "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['S/']['TPagado'], 2) . "</th>" . 
                    "<td colspan='2' style='text-align:right;'>Total Devolucion:</td>" . 
                    "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['S/']['totalDevolucion'], 2) . "</th>" . 
                    "<td colspan='2' style='text-align:right;'>Total Deuda:</td>" .
                    "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['S/']['totalDeuda']-$acumulaxIdMoneda_temporal['S/']['totalDevolucion'], 2) . "</th>" . 
                  "</tr>
                  <tr style='font-weight:bold;border-radius:10px;background-color:rgb(124, 180, 224)'>" . 
                    "<td colspan='2' style='text-align:right;'>Total:</td>" . 
                    "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['US $']['totalImporte'], 2) . "</th>" . 
                    "<td colspan='2' style='text-align:right;'>Total Pagado:</td>" . 
                    "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['US $']['TPagado'], 2) . "</th>" . 
                    "<td colspan='2' style='text-align:right;'>Total Devolucion:</td>" . 
                    "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['US $']['totalDevolucion'], 2) . "</th>" . 
                    "<td colspan='2' style='text-align:right;'>Total Deuda:</td>" .
                    "<th colspan='2' style='background-color:white;text-align:center;'>S/. " . number_format($acumulaxIdMoneda_temporal['US $']['totalDeuda']-$acumulaxIdMoneda_temporal['US $']['totalDevolucion'], 2) . "</th>" . 
                  "</tr>";
        }

        echo "</tbody>
                    <tfoot>
                           <tr><th colspan='2' style='text-align:right;'>Total</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalImporte'], 2) . "</td><th colspan='2' style='text-align:right;'>Total Pagado</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['TPagado'], 2) . "</td><th  style='text-align:right;' colspan='2'>Total Devolucion</th><td style='text-align:right;' colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalDevolucion'], 2) . "</td ><th colspan='2'>Total Deuda</th><td colspan='3'>S/. " . number_format($acumulaxIdMoneda['S/']['totalDeuda']-$acumulaxIdMoneda['S/']['totalDevolucion'], 2) . "</td></tr>
                           <tr><th colspan='2' style='text-align:right;'>Total</th><td colspan='2'>US $. " . number_format($acumulaxIdMoneda['US $']['totalImporte'], 2) . "</td><th colspan='2' style='text-align:right;'>Total Pagado</th><td colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['TPagado'], 2) . "</td><th  style='text-align:right;' colspan='2'>Total Devolucion</th><td style='text-align:right;' colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['totalDevolucion'], 2) . "</td ><th colspan='2'>Total Deuda</th><td colspan='3'>US $ " . number_format($acumulaxIdMoneda['US $']['totalDeuda']-$acumulaxIdMoneda['US $']['totalDevolucion'], 2) . "</td></tr>
                    </tfoot>
             ";
         
/*
              echo "</tbody>
                    <tfoot>
                           <tr>
                                <th colspan='1' style='text-align:right;font-size:11px !important;'>Total</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalImporte'], 2) . "</td>
                                <th colspan='1' style='text-align:right;font-size:11px !important;'>Total Pagado</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['TPagado'], 2) . "</td>
                                <th  style='text-align:right;font-size:11px !important;' colspan='1'>Total Devolucion</th><td style='text-align:right;' colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalDevolucion'], 2) . "</td >
                                <th colspan='2' style='text-align:right;font-size:11px !important;'>Total Deuda Sin Devoluciones</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalDeuda'], 2) . "</td>
                                <th colspan='2' style='text-align:right;font-size:11px !important;'>Total Deuda Con Devoluciones</th><td colspan='2'>S/. " . number_format($acumulaxIdMoneda['S/']['totalDeuda']-$acumulaxIdMoneda['S/']['totalDevolucion'], 2) . "</td>
                            </tr>
                           <tr>
                                <th colspan='1' style='text-align:right;font-size:11px !important;'>Total</th><td colspan='2'>US $. " . number_format($acumulaxIdMoneda['US $']['totalImporte'], 2) . "</td>
                                <th colspan='1' style='text-align:right;font-size:11px !important;'>Total Pagado</th><td colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['TPagado'], 2) . "</td>
                                <th  style='text-align:right;font-size:11px !important;' colspan='1'>Total Devolucion</th><td style='text-align:right;' colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['totalDevolucion'], 2) . "</td >
                                <th colspan='2' style='text-align:right;font-size:11px !important;'>Total Deuda Sin Devoluciones</th><td colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['totalDeuda'], 2) . "</td>
                                <th colspan='2' style='text-align:right;font-size:11px !important;'>Total Deuda Con Devoluciones</th><td colspan='2'>US $ " . number_format($acumulaxIdMoneda['US $']['totalDeuda']-$acumulaxIdMoneda['US $']['totalDevolucion'], 2) . "</td>
                           </tr>

                    </tfoot>";
  */      
    }

    function reporteventaspendientesvendedor() {
        $actor=$this->AutoLoadModel('actorrol');
        $data['vendedor']=$actor->actoresxRolxNombre(25);
        $this->view->show('/reporte/reporteventaspendientesvendedor.phtml',$data);
    }

}

?>
