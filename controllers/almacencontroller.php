<?php

class AlmacenController extends ApplicationGeneral {

    private $mostrar = 5;

    //Registro de orden de compra
    function regOrdenCompra() {
        $ordenCompra = new Ordencompra();
        $movimiento = new Movimiento();
        $data['Ordencompra'] = $ordenCompra->listadoOrdenecompraNoRegistrado();
        $data['Tipodocumento'] = $this->tipoDocumento();
        $this->view->show('/ordencompra/registrar.phtml', $data);
    }

    function buscar() {
        $Alm = New Almacen();
        $datos = $Alm->listado();
        $objeto = $this->formatearparakui($datos);
        header("Content-type: application/json");
        //echo "{\"data\":" .json_encode($objeto). "}";
        echo json_encode($objeto);
    }

    //Movimiento de Stock
    function movstock() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = 1;
        }
        session_start();
        $_SESSION['P_MovStock'] = "";
        $movimiento = new Movimiento();
        $tipooperacion = $this->AutoLoadModel('tipooperacion');
        $Movimiento = $movimiento->listaMovPaginado($id);
        $datos = array();
        $archivoConfig = parse_ini_file("config.ini", true);
        for ($i = 0; $i < count($Movimiento); $i++) {
            if ($Movimiento[$i]['tipomovimiento'] == 1) {
                $tipomovimiento = "INGRESO";
            } elseif ($Movimiento[$i]['tipomovimiento'] == 2) {
                $tipomovimiento = "SALIDA";
            } elseif ($Movimiento[$i]['tipomovimiento'] == 3) {
                $tipomovimiento = "SALDO INICIAL";
            }
            $datos[$i]['tipomovimiento'] = $tipomovimiento;
            $dataTipoOperacion = $tipooperacion->listadoTipoOperacionxId($Movimiento[$i]['idtipooperacion']);
            $datos[$i]['conceptomovimiento'] = $dataTipoOperacion[0]['nombre'];
            $datos[$i]['fechamovimiento'] = date('d/m/Y', strtotime($Movimiento[$i]['fechamovimiento']));
            $datos[$i]['ndocumento'] = $Movimiento[$i]['ndocumento'];
            $datos[$i]['observaciones'] = $Movimiento[$i]['observaciones'];
            $datos[$i]['idmovimiento'] = $Movimiento[$i]['idmovimiento'];
            $datos[$i]['serie'] = $Movimiento[$i]['serie'];
        }
        $data['valores'] = $datos;
        //$objeto=$this->formatearparakui($datos);
        //echo "{\"data\":" .json_encode($objeto). "}";
        $paginacion = $movimiento->paginadoMov();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/almacen/movimientostock.phtml", $data);
        //echo '<script> var datos='.json_encode($datos).';</script>';
    }

    //Movimiento de Stock Repuesto
    function movstockrep() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = 1;
        }
        session_start();
        $_SESSION['P_MovStock'] = "";
        $movimiento = new Movimiento();
        $tipooperacion = $this->AutoLoadModel('tipooperacion');
        $Movimiento = $movimiento->listaMovPaginadoRep($id);
        $datos = array();
        $archivoConfig = parse_ini_file("config.ini", true);
        for ($i = 0; $i < count($Movimiento); $i++) {
            if ($Movimiento[$i]['tipomovimiento'] == 1) {
                $tipomovimiento = "INGRESO";
            } elseif ($Movimiento[$i]['tipomovimiento'] == 2) {
                $tipomovimiento = "SALIDA";
            } elseif ($Movimiento[$i]['tipomovimiento'] == 3) {
                $tipomovimiento = "SALDO INICIAL";
            }
            $datos[$i]['tipomovimiento'] = $tipomovimiento;
            $dataTipoOperacion = $tipooperacion->listadoTipoOperacionxId($Movimiento[$i]['idtipooperacion']);
            $datos[$i]['fechamovimiento'] = date('d/m/Y', strtotime($Movimiento[$i]['fechamovimiento']));
            $datos[$i]['codigooc'] = $Movimiento[$i]['codigooc'];
            $datos[$i]['observaciones'] = $Movimiento[$i]['observaciones'];
            $datos[$i]['idrepuesto'] = $Movimiento[$i]['idrepuesto'];
        }
        $data['valores'] = $datos;//var_dump($datos);
        //$objeto=$this->formatearparakui($datos);
        //echo "{\"data\":" .json_encode($objeto). "}";
        $paginacion = $movimiento->paginadoMovRep();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/almacen/movimientorepuestostock.phtml", $data);
        //echo '<script> var datos='.json_encode($datos).';</script>';
    }

    function busca() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = 1;
        }
        session_start();
        $_SESSION['P_MovStock'];
        if (!empty($_REQUEST['txtBusqueda'])) {

            $_SESSION['P_linea'] = $_REQUEST['txtBusqueda'];
        }
        $parametro = $_SESSION['P_linea'];
        $movimiento = new Movimiento();
        $Movimiento = $movimiento->listaMovPaginado($id, $parametro);
        $TipoMovimiento = $this->tipoMovimiento();
        $datos = array();
        $archivoConfig = parse_ini_file("config.ini", true);
        for ($i = 0; $i < count($Movimiento); $i++) {
            $conceptoMovimiento = ($Movimiento[$i]['tipomovimiento'] == 1) ? $archivoConfig['Ingreso'] : $archivoConfig['Salida'];
            $datos[$i]['tipomovimiento'] = $TipoMovimiento[($Movimiento[$i]['tipomovimiento'])];
            $datos[$i]['conceptomovimiento'] = $conceptoMovimiento[($Movimiento[$i]['conceptomovimiento'])];
            $datos[$i]['fechamovimiento'] = date('d/m/Y', strtotime($Movimiento[$i]['fechamovimiento']));
            $datos[$i]['ndocumento'] = $Movimiento[$i]['ndocumento'];
            $datos[$i]['observaciones'] = $Movimiento[$i]['observaciones'];
            $datos[$i]['idmovimiento'] = $Movimiento[$i]['idmovimiento'];
        }
        $data['valores'] = $datos;
        //$objeto=$this->formatearparakui($datos);
        //echo "{\"data\":" .json_encode($objeto). "}";
        $data['retorno'] = $parametro;
        $data['totregistros'] = count($movimiento->listadoxParametro($parametro));
        $data['paginacion'] = $movimiento->paginadoMov($parametro);
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/almacen/busca.phtml", $data);
        //echo '<script> var datos='.json_encode($datos).';</script>';
    }
    function buscaRepuesto() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = 1;
        }
        session_start();
        $_SESSION['P_MovStock'];
        if (!empty($_REQUEST['txtBusqueda'])) {

            $_SESSION['P_linea'] = $_REQUEST['txtBusqueda'];
        }
        $parametro = $_SESSION['P_linea'];
        $movimiento = new Movimiento();
        $Movimiento = $movimiento->listaMovPaginadoRep($id, $parametro);
        $TipoMovimiento = $this->tipoMovimiento();
        $datos = array();
        $archivoConfig = parse_ini_file("config.ini", true);
        for ($i = 0; $i < count($Movimiento); $i++) {
            $conceptoMovimiento = ($Movimiento[$i]['tipomovimiento'] == 1) ? $archivoConfig['Ingreso'] : $archivoConfig['Salida'];
            $datos[$i]['tipomovimiento'] = $TipoMovimiento[($Movimiento[$i]['tipomovimiento'])];
            $datos[$i]['fechamovimiento'] = date('d/m/Y', strtotime($Movimiento[$i]['fechamovimiento']));
            $datos[$i]['codigooc'] = $Movimiento[$i]['codigooc'];
            $datos[$i]['observaciones'] = $Movimiento[$i]['observaciones'];
            $datos[$i]['idrepuesto'] = $Movimiento[$i]['idrepuesto'];
        }
        $data['valores'] = $datos;//var_dump($datos);
        //$objeto=$this->formatearparakui($datos);
        //echo "{\"data\":" .json_encode($objeto). "}";
        $data['retorno'] = $parametro;
        $data['totregistros'] = count($movimiento->listadoxParametroRep($parametro));
        $data['paginacion'] = $movimiento->paginadoMovRep($parametro);
        $data['blockpaginas'] = round($paginacion / 10);//var_dump($data);
        $this->view->show("/almacen/buscaRepuesto.phtml", $data);
        //echo '<script> var datos='.json_encode($datos).';</script>';
    }

    function registra() {
        $dataMovimiento = $_REQUEST['Movimiento'];
        $idOrdenCompra = $dataMovimiento[idordencompra];
        $numero = $_REQUEST['txtCantidadDetalleMovimiento'];
        $producto = new Producto();
        $movimiento = new Movimiento();
        $detalleMovimiento = new Detallemovimiento();
        $ordenCompra = new Ordencompra();
        $detalleOrdenCompra = new Detalleordencompra();
        $historialProducto = new Historialproducto();
        $exitoMovimiento = $movimiento->grabaMovimiento($dataMovimiento);
        $operacion = ($dataMovimiento['idtipomovimiento'] == 1) ? "+" : "-";
        //Si el concepto movimiento es ingreso por compras
        if ($dataMovimiento['idordencompra']) {
            $dataOrdenCompra = array('registrado' => '1');
            $exitoOrdenCompra = $ordenCompra->actualizaOrdenCompra($dataOrdenCompra, $idOrdenCompra);
            for ($i = 1; $i <= $numero; $i++) {
                $datosDetalleMovimiento = $_REQUEST['Detallemovimiento' . $i];
                $datosProducto = $_REQUEST['Producto' . $i];
                if ($datosDetalleMovimiento['estado']) {
                    $cantidad = $datosDetalleMovimiento['cantidadrecibida'];
                    $idProducto = $datosDetalleMovimiento['idproducto'];
                    $iddetalleordencompra = $datosDetalleMovimiento['iddetalleordencompra'];
                    //Obtenemos el stock actual y agregamos el elemento stockactual al
                    //array $datosProducto y lo actualizamos la tabla wc_producto
                    $dataProducto = $producto->buscaProducto($idProducto);
                    $datosProducto['stockactual'] = $dataProducto[0]['stockactual'] + $cantidad;
                    $exitoProducto = $producto->actualizaProducto($datosProducto, $idProducto);
                    //Actualizamos la cantidadrecibida de la tabla wc_detalleordencompra
                    $cantidadRecibida = array('cantidadrecibida' => $cantidad);
                    $exitoDetalleOrdenCompra = $detalleOrdenCompra->actualizaDetalleOrdenCompra($cantidadRecibida, $iddetalleordencompra);
                    //Preparar el array $datosProducto para insertar registro en al tabla
                    //wc_historialproducto. Eliminamos los elemtos stockactual y preciolista
                    //luego agregamos idproducto y cantidad.
                    unset($datosProducto['stockactual']);
                    unset($datosProducto['preciolista']);
                    $datosProducto['idproducto'] = $idProducto;
                    $datosProducto['cantidad'] = $cantidad;
                    $datosProducto['fecha'] = date('Y/m/d');
                    $exitoHistorialProducto = $historialProducto->grabaHistorialProducto($datosProducto);
                    //Preparamos el array $datosDetalleMovimiento para insertar registro en la
                    //tabla wc_detallemovimiento.
                    unset($datosDetalleMovimiento['iddetalleordencompra']);
                    unset($datosDetalleMovimiento['estado']);
                    array_splice($datosDetalleMovimiento, -2);
                    $datosDetalleMovimiento['cantidad'] = $cantidad;
                    $exitoDetalleMovimiento = $detalleMovimiento->grabaDetalleMovimieto($datosDetalleMovimiento);
                }
            }
            if ($exitoMovimiento and $exitoOrdenCompra and $exitoProducto and $exitoDetalleOrdenCompra and $exitoHistorialProducto and $exitoDetalleMovimiento) {
                $ruta['ruta'] = "/movimiento/listar";
                $this->view->show("ruteador.phtml", $ruta);
            }
        } else {
            $dataDetalleMovimiento = array_splice($_REQUEST, 3, count($_REQUEST) - 8);
            foreach ($dataDetalleMovimiento as $data) {
                $cantidad = $data[cantidad];
                $idProducto = $data[idproducto];
                $dataProducto = $producto->buscaProducto($idProducto);
                $stockActual = $dataProducto[0]['stockactual'];
                $stockNuevo = ($operacion == '+') ? array('stockactual' => $stockActual + $cantidad) : array('stockactual' => $stockActual - $cantidad);
                $exitoDetalleMovimiento = $detalleMovimiento->grabaDetalleMovimieto($data);
                $exitoProducto = $producto->actualizaProducto($stockNuevo, $idProducto);
            }
            if ($exitoMovimiento and $exitoDetalleMovimiento and $exitoProducto) {
                $ruta['ruta'] = "/movimiento/listar";
                $this->view->show("ruteador.phtml", $ruta);
            }
        }
    }

    function nuevo() {
        $this->view->show("almacen/nuevo.phtml");
    }

    function graba() {
        $data = $_REQUEST['Almacen'];
        $almacen = new Almacen();
        $data['estado'] = 1;
        $exito = $almacen->grabaAlmacen($data);
        if ($exito) {
            $ruta['ruta'] = "/almacen/listar/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function editar() {
        $idAlmacen = $_REQUEST['id'];
        $almacen = new Almacen();
        $data['Almacen'] = $almacen->buscaAlmacen($idAlmacen);
        $this->view->show("/almacen/editar.phtml", $data);
    }

    function actualiza() {
        $almacen = new Almacen();
        $data = $_REQUEST['Almacen'];
        $idalmacen = $_REQUEST['idAlmacen'];
        $almacen = new Almacen();
        $exito = $almacen->actualizaAlmacen($data, "idalmacen=" . $idalmacen);
        if ($exito) {
            $ruta['ruta'] = "/almacen/listar/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function eliminar() {
        $idAlmacen = $_REQUEST['id'];
        $almancen = new Almacen();
        $exito = $almancen->cambiaEstadoAlmacen($idAlmacen);
        if ($exito) {
            $ruta['ruta'] = "/almacen/listar/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function autocompletealmacen() {
        $almacen = new Almacen();
        $text = $_REQUEST['id'];
        $datos = $almacen->buscaautocomplete($text);
        echo json_encode($datos);
    }

    function confirmaOrden() {
        if (!$_REQUEST['idOrdenVenta']) {
            $ordenVenta = new OrdenVenta();
            $opciones = new general();
            $url = "/" . $_REQUEST['url'];
            $data['Opcion'] = $opciones->buscaOpcionexurl($url);
            $data['Modulo'] = $opciones->buscaModulosxurl($url);
            $data['ordenVenta'] = $ordenVenta->pedidoxaprobar(4);
            $data['FormaPago'] = $this->formaPago();
            $this->view->show("/almacen/confirmaorden.phtml", $data);
        } else {
            $id = $_REQUEST['idOrdenVenta'];
            $estadoOrden = $_REQUEST['estadoOrden'];
            $dataOrdenVenta = $_REQUEST['Orden'];
            $dataDetalleOrdenVenta = $_REQUEST['DetalleOrdenVenta'];
            $productos = $_REQUEST['Producto'];
            $ordenVenta = new OrdenVenta();
            $detalleOrdenVenta = new DetalleOrdenVenta();
            $producto = new Producto();
            $dataOrdenVenta['vbalmacen'] = ($estadoOrden == 1) ? 1 : 2;
            if ($dataOrdenVenta['vbalmacen'] == 2) {
                $dataOrdenVenta['desaprobado'] = 1;
            }
            $exito1 = $ordenVenta->actualizaOrdenVenta($dataOrdenVenta, $id);
            $cont = 0;
            if ($exito1) {
                foreach ($dataDetalleOrdenVenta as $data) {
                    if ($dataOrdenVenta['vbalmacen'] == 2 || $data['estado'] == 0) {
                        //buscamos producto
                        $idproducto = $productos[$cont]['idproducto'];
                        $dataProducto = $producto->buscaProductoxId($idproducto);
                        $stockdisponibleA = $dataProducto[0]['stockdisponible'];
                        $stockdisponibleN = $stockdisponibleA + $productos[$cont]['cantaprobada'];
                        $dataNuevo['stockdisponible'] = $stockdisponibleN;
                        //actualizamos es stockdisponible
                        $exitoP = $producto->actualizaProducto($dataNuevo, $idproducto);
                    } elseif ($data['estado'] == 1) {
                        //buscamos producto
                        $idproducto = $productos[$cont]['idproducto'];
                        $dataProducto = $producto->buscaProductoxId($idproducto);
                        $stockdisponibleA = $dataProducto[0]['stockdisponible'];
                        $stockdisponibleN = $stockdisponibleA + $productos[$cont]['cantaprobada'] - $data['cantdespacho'];
                        $dataNuevo['stockdisponible'] = $stockdisponibleN;
                        //actualizamos es stockdisponible
                        $exitoP = $producto->actualizaProducto($dataNuevo, $idproducto);
                    }
                    $exito2 = $detalleOrdenVenta->actualizar($data['iddetalleordenventa'], $data);
                    $cont++;
                }
                if ($exito2) {
                    //graba el tiempo que demoro ser confirmado
                    $ordenVentaDuracion = new ordenventaduracion();
                    $DDA = $ordenVentaDuracion->listaOrdenVentaDuracion($id, "cobranza");
                    $dataDuracion['idordenventa'] = $id;
                    $intervalo = $this->date_diff(date('Y-m-d H:i:s', strtotime($DDA[0]['fechacreacion'])), date('Y-m-d H:i:s'));
                    $dataDuracion['tiempo'] = $intervalo;
                    if (empty($DDA[0]['fechacreacion'])) {
                        $dataDuracion['tiempo'] = 'indifinido';
                    }
                    $dataDuracion['referencia'] = 'almacen';
                    $exitoN = $ordenVentaDuracion->grabaOrdenVentaDuracion($dataDuracion);
                    //actualiza ordenventa su duracion total
                    $DDAT = $ordenVentaDuracion->listaOrdenVentaDuracion($id, "creacion");
                    $fechaInicio = $DDAT[0]['fechacreacion'];
                    if (empty($fechaInicio)) {
                        $dt = $ordenVenta->buscaOrdenVenta($id);
                        $fechaInicio = $dt[0]['fechacreacion'];
                    }
                    $intervalo2 = $this->date_diff(date('Y-m-d H:i:s', strtotime($fechaInicio)), date('Y-m-d H:i:s'));
                    $DOV['tiempoduracion'] = $intervalo2;
                    $exitoN2 = $ordenVenta->actualizaOrdenVenta($DOV, $id);
                    $ruta['ruta'] = "/almacen/confirmaorden";
                    $this->view->show("ruteador.phtml", $ruta);
                }
            }
        }
    }

    function listar() {
        $almacen = $this->AutoLoadModel('almacen');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_Almacen'] = "";

        $data['Almacen'] = $almacen->listaAlmacenPaginado($_REQUEST['id']);
        $paginacion = $almacen->paginadoAlmacen();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/almacen/listar.phtml", $data);
    }

    function grabaJason() {
        $linea = $this->AutoLoadModel('almacen');
        $data['nomalm'] = $_REQUEST['nomalm'];
        $data['razsocalm'] = $_REQUEST['razsocalm'];
        $data['diralm'] = $_REQUEST['diralm'];
        $data['rucalm'] = $_REQUEST['rucalm'];
        $data['estado'] = 1;
        $exito = $linea->grabaAlmacen($data);
        if ($exito) {
            $dataResp['valid'] = true;
            $dataResp['resp'] = 'Dato Agregado';
            $dataResp['idAlmacen'] = $exito;
            echo json_encode($dataResp);
        } else {
            $dataResp['valid'] = false;
            $dataResp['resp'] = 'No se pudo Agregar';
            echo json_encode($dataResp);
        }
    }

    function regOrdenSalida() {
        if (!$_REQUEST['idOrdenVenta']) {
            $ordenVenta = new OrdenVenta();
            $opciones = new general();
            $actorrol = new actorrol();
            $url = "/" . $_REQUEST['url'];
            $data['Despachador'] = $actorrol->actoresxRol(30);
            $data['Verificador'] = $actorrol->actoresxRol(31);
            $data['Opcion'] = $opciones->buscaOpcionexurl($url);
            $data['Modulo'] = $opciones->buscaModulosxurl($url);
            $data['ordenVenta'] = $ordenVenta->pedidoxaprobar(5);
            $data['FormaPago'] = $this->formaPago();
            $this->view->show("/almacen/regordenpedido.phtml", $data);
        } else {
            $id = $_REQUEST['idOrdenVenta'];
            $dataOrdenVenta = $_REQUEST['Orden'];
            $ordenVenta = new OrdenVenta();
            $exito1 = $ordenVenta->actualizaOrdenVenta($dataOrdenVenta, $id);
            if ($exito1) {
                //grabamos las series
                $detOrden = $this->AutoLoadModel('detalleordenventa');
                $detalleOrdenVenta = $_REQUEST['DetalleOrdenVenta'];
                $cantidad = count($detalleOrdenVenta);
                for ($i = 0; $i < $cantidad; $i++) {
                    $data['serie'] = $detalleOrdenVenta[$i]['serie'];
                    $exito2 = $detOrden->actualizar($detalleOrdenVenta[$i]['iddetalleordenventa'], $data);
                }
                //graba el tiempo que demoro ser confirmado
                $ordenVentaDuracion = new ordenventaduracion();
                $DDA = $ordenVentaDuracion->listaOrdenVentaDuracion($id, "credito");
                $dataDuracion['idordenventa'] = $id;
                $intervalo = $this->date_diff(date('Y-m-d H:i:s', strtotime($DDA[0]['fechacreacion'])), date('Y-m-d H:i:s'));
                $dataDuracion['tiempo'] = $intervalo;
                if (empty($DDA[0]['fechacreacion'])) {
                    $dataDuracion['tiempo'] = 'indefinido';
                }
                $dataDuracion['referencia'] = 'despacho';
                $exitoN = $ordenVentaDuracion->grabaOrdenVentaDuracion($dataDuracion);
                //actualiza ordenventa su duracion total
                $DDAT = $ordenVentaDuracion->listaOrdenVentaDuracion($id, "creacion");
                $fechaInicio = $DDAT[0]['fechacreacion'];
                if (empty($fechaInicio)) {
                    $dt = $ordenVenta->buscaOrdenVenta($id);
                    $fechaInicio = $dt[0]['fechacreacion'];
                }
                $intervalo2 = $this->date_diff(date('Y-m-d H:i:s', strtotime($fechaInicio)), date('Y-m-d H:i:s'));
                $DOV['tiempoduracion'] = $intervalo2;
                $exitoN2 = $ordenVenta->actualizaOrdenVenta($DOV, $id);
                $ruta['ruta'] = "/almacen/regOrdenSalida";
                $this->view->show("ruteador.phtml", $ruta);
            }
        }
    }

    function despacho() {
        $idGuia = $_REQUEST['id'];
        $cliente = New Cliente();
        $ordencobro = New OrdenCobro();
        $actorRol = New actorRol();
        $dataCliente = $cliente->buscaxOrdenVenta($idGuia);
        $iddespachador = $dataCliente[0]['iddespachador'];
        $idverificador = $dataCliente[0]['idverificador'];
        $idverificador2 = $dataCliente[0]['idverificador2'];
        $dataDespachador = $actorRol->buscaActorxRol($iddespachador);
        $dataVerificador = $actorRol->buscaActorxRol($idverificador);
        $dataVerificador2 = $actorRol->buscaActorxRol($idverificador2);
        $detalleOrdenVenta = new detalleOrdenVenta();
        $data = $detalleOrdenVenta->listaDetalleOrdenVenta($idGuia);
        $cantidadDetalles = count($data);
        session_start();
        $usuario = $_SESSION['nombrecompleto'];
        $columna .= '<table>
			<thead>
                            <tr>
				<th Colspan="8">Orden de Venta N° ' . $dataCliente[0]['codigov'] . '<input type="hidden" value="' . $dataCliente[0]['idordenventa'] . '" id="idordenventa"></th>						
                            </tr>
                            <tr>
				<th>Usuario</th>
				<td colspan="8">' . $usuario . '</td>
                            </tr>
                            <tr>
				<th>Despachador</th>
				<td>' . $dataDespachador[0]['nombres'] . ' ' . $dataDespachador[0]['apellidopaterno'] . ' ' . $dataDespachador[0]['apellidomaterno'] . '</td>
				<th>Verificador</th>
				<td>' . $dataVerificador[0]['nombres'] . ' ' . $dataVerificador[0]['apellidopaterno'] . ' ' . $dataVerificador[0]['apellidomaterno'] . '</td>
				<th>Re-Chequeador</th>
				<td>' . $dataVerificador2[0]['nombres'] . ' ' . $dataVerificador2[0]['apellidopaterno'] . ' ' . $dataVerificador2[0]['apellidomaterno'] . '</td>
				<th>Vendedor</th>
				<td>' . $dataCliente[0]['vendedor'] . '</td>
                            </tr>
                            <tr>
                                <th>Fecha Despacho</th>
				<td>' . $dataCliente[0]['fechadespacho'] . '</td>
				<th>Nro Cajas</th>
				<td>' . $dataCliente[0]['nrocajas'] . '</td>
				<th>Nro Bultos</th>
				<td>' . $dataCliente[0]['nrobultos'] . '</td>
				<th>Fecha y Hora de Impresion</th>
				<td>' . date('d-m-Y H:j:s') . '</td>								
                            </tr>
                            <tr>
                                <th style="background:white;" Colspan="6">&nbsp</th>
                            </tr>
                            <tr>
                                <th >N°</th>
				<th >Codigo</th>
				<th colspan=3>Nombre Producto</th>
				<th >Cantidad Solicitada</th>
				<th >Cantidad Aprobada</th>
				<th >Cantidad Despachada</th>
                            </tr>
			</thead>
                        <tbody>';
        for ($i = 0; $i < $cantidadDetalles; $i++) {
            $columna .= '<tr>
                            <td>' . ($i + 1) . '</td>
                            <td>' . $data[$i]['codigopa'] . '</td>
                            <td colspan=3>' . $data[$i]['nompro'] . '</td>
                            <td style="text-align:center;">' . $data[$i]['cantsolicitada'] . '</td>
                            <td style="text-align: center;">' . $data[$i]['cantaprobada'] . '</td>
                            <td style="text-align: center;">' . $data[$i]['cantdespacho'] . '</td>
			</tr>';
        }
        $columna .= '   </tbody>
                    <table>';
        echo $columna;
    }

    function buscarDespacho() {
        $this->view->show('/almacen/impresionDespacho.phtml', $data);
    }

    function buscaAlmacen() {
        $almacen = $this->AutoLoadModel('almacen');
        $idalmacen = $_REQUEST['idalmacen'];
        $dataAlmacen = $almacen->buscaAlmacen($idalmacen);
        echo json_encode($dataAlmacen[0]);
    }

    function listaAlmacen() {
        $almacen = $this->AutoLoadModel('almacen');
        $dataBusqueda = $almacen->listado();
        $cantidad = count($dataBusqueda);
        $option = "<option value=''>Seleccione Almacen</option>";
        for ($i = 0; $i < $cantidad; $i++) {
            $option .= "<option value=" . $dataBusqueda[$i]['idalmacen'] . ">" . $dataBusqueda[$i]['nomalm'] . "</option>";
        }
        echo $option;
    }

    function corrigueKardex() {
        $this->view->show('/almacen/corrigeKardex.phtml', $data);
    }

    function correccionKardex() {
        $mantenimiento = $this->configIni('Globals', 'mantenimiento');
        if ($mantenimiento == 0) {
            $idProducto = $_REQUEST['idProducto'];
            $movimiento = $this->AutoLoadModel('movimiento');
            $detalleMovimiento = $this->AutoLoadModel('detallemovimiento');
            $producto = $this->AutoLoadModel('producto');
            $detalleOrdenVenta = $this->AutoLoadModel('detalleordenventa');
            $detalleOrdenCompra = $this->AutoLoadModel('detalleordencompra');
            $filtro = "dm.estado=1 and m.estado=1 and idproducto='$idProducto' ORDER BY m.fechamovimiento,dm.iddetallemovimiento";
            $dataDetalleMovimiento = $detalleMovimiento->buscaDetalleMovimientoxFiltro($filtro);
            $dataOrdenCompra = $detalleOrdenCompra->sumaCantidadProducto("oc.registrado!=1 and doc.estado=1 and oc.estado=1 and idproducto='$idProducto'");
            $dataOrdenVenta = $detalleOrdenVenta->sumaCantidadProducto("ov.vbventas=1 and desaprobado!=1 and ov.vbalmacen!=1 and dov.estado=1 and ov.estado=1 and idproducto='$idProducto'");
            $cantidadData = count($dataDetalleMovimiento);
            $cantidadInicial = round($dataDetalleMovimiento[0]['stockactual']);
            for ($i = 0; $i < $cantidadData; $i++) {
                if ($i > 0) {
                    $idDetalleMovimiento = $dataDetalleMovimiento[$i]['iddetallemovimiento'];
                    if ($dataDetalleMovimiento[$i]['tipomovimiento'] == 1) {
                        $data['stockactual'] = $cantidadInicial + $dataDetalleMovimiento[$i]['cantidad'];
                    } else if ($dataDetalleMovimiento[$i]['tipomovimiento'] == 2) {
                        $data['stockactual'] = $cantidadInicial - $dataDetalleMovimiento[$i]['cantidad'];
                    }
                    $exito = $detalleMovimiento->actualizaDetalleMovimientoxid($idDetalleMovimiento, $data);
                    $cantidadInicial = $data['stockactual'];
                }
            }
            $data['stockdisponible'] = $data['stockactual'] + $dataOrdenCompra - $dataOrdenVenta;
            $exitoP = $producto->actualizaProducto($data, $idProducto);
            if ($exito) {
                $this->view->show('/almacen/corrigeKardex.phtml', $data);
            }
        } else {
            $this->view->show('/almacen/corrigeKardex.phtml', $data);
        }
    }

}
?>