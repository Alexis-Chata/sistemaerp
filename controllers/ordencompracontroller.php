<?php

class OrdencompraController extends ApplicationGeneral {

    function listar() {
        $ordenCompra = new Ordencompra();
        $data['Ordencompra'] = $ordenCompra->listadoOrdenescompra();
        $this->view->show("/ordencompra/listar.phtml", $data);
    }

    function listarOptions() {
        $ordenCompra = new Ordencompra();
        $data = $ordenCompra->listadoOrdenecompraNoRegistrado();
        for ($i = 0; $i < count($data); $i++) {
            $codigo = "0000" . $data[$i]['idordencompra'];
            echo '<option value="' . $data[$i]['idordencompra'] . '">' . substr($codigo, strlen($codigo) - 5) . '</option>';
        }
    }

    function graba() {
        $dataOrdenCompra = $_REQUEST['Ordencompra'];
        $dataOrdenCompra['faproxllegada'] = date('Y-m-d', strtotime($dataOrdenCompra['faproxllegada']));
        $dataOrdenCompra['nuevoformato'] = 1;
        $dataDetalleOrdenCompra = $_REQUEST['Detalleordencompra'];
        $ordenCompra = new Ordencompra();
        $detalleOrdenCompra = new Detalleordencompra();
        $producto = new Producto();
        $dataOrdenCompra['estado'] = 1;
        $exito1 = $ordenCompra->grabaOrdenCompra($dataOrdenCompra);
        if ($exito1) {
            $codigooc = strtoupper($ordenCompra->generaCodigo());
            $dataOrden['codigooc'] = $codigooc;
            $actualiza = $ordenCompra->actualizaOrdenCompra($dataOrden, $exito1);
            foreach ($dataDetalleOrdenCompra as $data) {
                $data['idordencompra'] = $exito1;
                $data['cantidadrecibidaoc'] = $data['cantidadsolicitadaoc'];
                $dataProducto = $producto->buscaProducto($data['idproducto']);
                $stockDisponible['stockdisponible'] = $dataProducto[0]['stockdisponible'] + $data['cantidadsolicitadaoc'];
                $exito2 = $detalleOrdenCompra->grabaDetalleOrdenCompra($data);
                $exito3 = $producto->actualizaProducto($stockDisponible, $data['idproducto']);
            }
            $ordenCompraVendedor = new Ordencompravendedor();
            $dataColaboradores = $_REQUEST['idcolaborades'];
            $tam = count($dataColaboradores);
            $dataOCV['idordencompra'] = $exito1;
            for ($i = 0; $i < $tam; $i++) {
                $dataOCV['idvendedor'] = $dataColaboradores[$i];
                $ordenCompraVendedor->graba($dataOCV);
            }
            if ($exito2 and $exito3) {
                $ruta['ruta'] = "/ordencompra/vistaRespuesta/" . $codigooc;
                $this->view->show("ruteador.phtml", $ruta);
            }
        }
    }

    function editar() {
        $id = $_REQUEST['id'];
        if (!empty($_REQUEST['id']) && $_REQUEST['id'] > 0) {
            $ordenCompra = new Ordencompra();
            $dataOrdencompra = $ordenCompra->editaOrdenCompra($id);
            if ($dataOrdencompra[0]['vbimportaciones'] == 0) {
                $detalleOrdenCompra = new Detalleordencompra();
                $almacen = new Almacen();
                $proveedor = new Proveedor();
                $rutaImagen = $this->rutaImagenesProducto();           
                $dataJefedeLinea = $ordenCompra->buscarVendedorXOrdenCompra($id);
                $data['JefeLinea'] = '';
                if (count($dataJefedeLinea) > 0) {
                    $data['JefeLinea'] = $dataJefedeLinea[0]['nombres'] . ' ' . $dataJefedeLinea[0]['apellidopaterno'] . ' ' . $dataJefedeLinea[0]['apellidomaterno'];
                }
                $ordenCompraVendedor = new Ordencompravendedor();
                $dataColaboradores = $ordenCompraVendedor->buscarColaboradorXOC($id);
                $tamColaboradores = count($dataColaboradores);            

                $data['Colaboradores'] = '';               
                for ($i = 0; $i < $tamColaboradores; $i++) {
                    $data['Colaboradores'] .= '<li class="EliminarColaborador">' .
                                                    '<input type="hidden" value="' . $dataColaboradores[$i]['idactor'] . '" id="idColaborado_' . $dataColaboradores[$i]['idactor'] . '" name="idcolaborades[]">' .
                                                    '<input type="text" title="Eliminar Colaborador" value="' . $dataColaboradores[$i]['nombres'] .  " " . $dataColaboradores[$i]['apellidopaterno'] . " " . $dataColaboradores[$i]['apellidomaterno'] . '" size="25" class="inputBorder" readonly="">' .
                                               '</li>';
                }        
            
                $data['Ordencompra'] = $dataOrdencompra;
                $data['Detalleordencompra'] = $detalleOrdenCompra->listaDetalleOrdenCompra($id);
                $data['Empresa'] = $almacen->listadoAlmacen();
                $data['RutaImagen'] = $rutaImagen;
                $data['Proveedor'] = $proveedor->listadoProveedores();
                $this->view->show("/ordencompra/editar.phtml", $data);
            } else {
                $ruta['ruta'] = "/importaciones/ordencompra";
                $this->view->show("ruteador.phtml", $ruta);
            }            
        } else {
            $ruta['ruta'] = "/importaciones/ordencompra";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function valorizarOrden() {
        $id = $_REQUEST['id'];
        if (!empty($_REQUEST['id']) && $_REQUEST['id'] > 0) {
            $ordenCompra = new Ordencompra();
            $detalleOrdenCompra = new Detalleordencompra();
            $almacen = new Almacen();
            $proveedor = new Proveedor();
            $empresa = $this->AutoLoadModel('empresa');
            $rutaImagen = $this->rutaImagenesProducto();
            $dataOrdencompra= $ordenCompra->editaOrdenCompra($id);
            $data['Ordencompra'] = $dataOrdencompra;
            $data['Detalleordencompra'] = $detalleOrdenCompra->listaDetalleOrdenCompra($id);
            /* echo '<pre>';
              print_r($data['Ordencompra']);
              exit; */
            $data['Empresa'] = $almacen->listadoAlmacen();
            $data['RutaImagen'] = $rutaImagen;

            $data['Proveedor'] = $proveedor->listadoProveedores();
            $data['Flete'] = $empresa->listadoEmpresaxIdTipoEmpresa(1);
            $data['Aduanas'] = $empresa->listadoEmpresaxIdTipoEmpresa(3);
            $data['Seguro'] = $empresa->listadoEmpresaxIdTipoEmpresa(2);
            if ($dataOrdencompra[0]['nuevoformato']==1) {
                $dataJefedeLinea = $ordenCompra->buscarVendedorXOrdenCompra($id);
                $data['JefeLinea'] = '';
                if (count($dataJefedeLinea) > 0) {
                    $data['JefeLinea'] = '<input type="text" value="' . $dataJefedeLinea[0]['nombres'] .  " " . $dataJefedeLinea[0]['apellidopaterno'] . " " . $dataJefedeLinea[0]['apellidomaterno'] . '" size="25" class="inputBorder" readonly="">';
                }
                $ordenCompraVendedor = new Ordencompravendedor();
                $dataColaboradores = $ordenCompraVendedor->buscarColaboradorXOC($id);
                $tamColaboradores = count($dataColaboradores);            

                $data['Colaboradores'] = '<ul>';               
                for ($i = 0; $i < $tamColaboradores; $i++) {
                    $data['Colaboradores'] .= '<li>' .                                                        
                                                    '<input type="text" value="' . $dataColaboradores[$i]['nombres'] .  " " . $dataColaboradores[$i]['apellidopaterno'] . " " . $dataColaboradores[$i]['apellidomaterno'] . '" size="25" class="inputBorder" readonly="">' .
                                               '</li>';
                }
                $data['Colaboradores'] .= '</ul>';     
                $this->view->show("/ordencompra/valorizarOrdenNuevo.phtml", $data);
            } else {
                $this->view->show("/ordencompra/valorizarOrden.phtml", $data);
            }            
        } else {
            $ruta['ruta'] = "/importaciones/ordencompra";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function actualiza() {
        $idOrdenCompra = $_REQUEST['idOrdenCompra'];
        $dataOrdenCompra = $_REQUEST['Ordencompra'];
        $dataOrdenCompra['nuevoformato'] = 1;
        $dataOrdenCompra['faproxllegada'] = date('Y-m-d', strtotime($dataOrdenCompra['faproxllegada']));
        $dataDetalleOrdenCompra = $_REQUEST['Detalleordencompra'];
        $DProducto = $_REQUEST['Producto'];
        $ordenCompra = new Ordencompra();
        $detalleOrdenCompra = new Detalleordencompra();
        $producto = new Producto();
        $exito1 = $ordenCompra->actualizaOrdenCompra($dataOrdenCompra, $idOrdenCompra);
        $cont = 0;
        if ($exito1) {
            foreach ($dataDetalleOrdenCompra as $data) {
                $cont++;
                $data['idordencompra'] = $idOrdenCompra;
                $data['cantidadrecibidaoc'] = $data['cantidadsolicitadaoc'];
                if ($data['iddetalleordencompra']) {
                    if ($data['estado'] != 1) {
                        $cantidad = $DProducto[$cont]['cantidad'];
                        $idProducto = $data['idproducto'];
                        $dataProducto = $producto->buscaProducto($idProducto);
                        $stockDisponible = $dataProducto[0]['stockdisponible'];
                        $dataP['stockdisponible'] = $stockDisponible - $cantidad;
                        $exito = $producto->actualizaProducto($dataP, $idProducto);
                    } elseif ($data['estado'] == 1) {
                        $cantidad = $DProducto[$cont]['cantidad'];
                        $idProducto = $data['idproducto'];
                        $dataProducto = $producto->buscaProducto($idProducto);
                        $stockDisponible = $dataProducto[0]['stockdisponible'];
                        $dataP['stockdisponible'] = $stockDisponible - $cantidad + $data['cantidadsolicitadaoc'];
                        $exito = $producto->actualizaProducto($dataP, $idProducto);
                    }
                    $exito2 = $detalleOrdenCompra->actualizaDetalleOrdenCompra($data, $data['iddetalleordencompra']);
                } else {
                    $exito2 = $detalleOrdenCompra->grabaDetalleOrdenCompra($data);

                    $idProducto = $data['idproducto'];
                    $dataProducto = $producto->buscaProducto($idProducto);
                    $stockDisponible = $dataProducto[0]['stockdisponible'];
                    $dataP['stockdisponible'] = $stockDisponible + $data['cantidadsolicitadaoc'];
                    $exito = $producto->actualizaProducto($dataP, $idProducto);
                }
            }
            $ordenCompraVendedor = new Ordencompravendedor();
            $dataColaboradores = $_REQUEST['idcolaborades'];
            $dataOCVActualizar['estado'] = 0;
            $ordenCompraVendedor->actualiza($dataOCVActualizar, "idordencompra='$idOrdenCompra'");
            $tam = count($dataColaboradores);
            $dataOCVActualizar['idordencompra'] = $idOrdenCompra;
            $dataOCVActualizar['estado'] = 1;
            for ($i = 0; $i < $tam; $i++) {
                $AuxOCV = $ordenCompraVendedor->buscarXfiltro("idordencompra='$idOrdenCompra' and idvendedor='$dataColaboradores[$i]'");
                $dataOCVActualizar['idvendedor'] = $dataColaboradores[$i];
                if (count($AuxOCV) > 0) {                    
                    $ordenCompraVendedor->actualiza($dataOCVActualizar, "idordencompravendedor='" . $AuxOCV[0]['idordencompravendedor'] . "'");
                } else {                    
                    $ordenCompraVendedor->graba($dataOCVActualizar);
                }                
            }
            if ($exito2) {
                $ruta['ruta'] = "/importaciones/ordencompra";
                $this->view->show("ruteador.phtml", $ruta);
            }
        }
    }
    
    function elimina() {
        $id = $_REQUEST['id'];
        $ordenCompra = new Ordencompra();
        $detalleOrdenCompra = new Detalleordencompra();
        $producto = new Producto();
        //buscamos sus detalles de la orden de compra que le perntenece para aumentar el stockdisponible
        $dataDetalle = $detalleOrdenCompra->buscaDetalleOrdenCompra($id);
        $cantidad = count($dataDetalle);
        for ($i = 0; $i < $cantidad; $i++) {
            $cantidadsolicitadaoc = $dataDetalle[$i]['cantidadsolicitadaoc'];
            $idProducto = $dataDetalle[$i]['idproducto'];
            $dataProducto = $producto->buscaProducto($idProducto);
            $stockDisponible = $dataProducto[0]['stockdisponible'];
            $data['stockdisponible'] = $stockDisponible - $cantidadsolicitadaoc;
            $exito = $producto->actualizaProducto($data, $idProducto);
        }
        $estado = $ordenCompra->eliminaOrdenCompra($id);
        if ($estado) {
            $ruta['ruta'] = "/importaciones/ordencompra";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function ordenCompraVendedor() {
        $idProveedor = $_REQUEST['idProveedor'];
        $fecha = $_REQUEST['fecha'];
        $fechaInicio = $_REQUEST['fechaInicio'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        $repote = new Reporte();
        $data = $repote->reporteOrdenCompra($idProveedor, $fecha, $fechaInicio, $fechaFinal);
        for ($i = 0; $i < count($data); $i++) {
            echo "<tr>";
            echo "<td>" . $data[$i]['codigov'] . "</td>";
            echo '<td>' . date("d/m/Y", strtotime($data[$i]['fordencompra'])) . '</td>';
            echo "<td>" . $data[$i]['razonsocial'] . "</td>";
            echo "<td>" . $data[$i]['nombrezona'] . "</td>";
            echo "<td>" . $data[$i]['direccion'] . "</td>";
            $imagen1 = ($vbVentas == 0) ? '' : (($vbVentas == 1) ? '/imagenes/iconos/aprobado.jpg' : '/imagenes/iconos/desaprobado.jpg');
            $imagen2 = ($vbCobranza == 0) ? '' : (($vbCobranza == 1) ? '/imagenes/iconos/aprobado.jpg' : '/imagenes/iconos/desaprobado.jpg');
            $imagen3 = ($vbCreditos == 0) ? '' : (($vbCreditos == 1) ? '/imagenes/iconos/aprobado.jpg' : '/imagenes/iconos/desaprobado.jpg');
            echo '<td><img src="' . $imagen1 . '"</td>';
            echo '<td><img src="' . $imagen2 . '"</td>';
            echo '<td><img src="' . $imagen3 . '"</td>';
            echo '<td width="100px">' .
            '<a href="/almacen/editar/' . $data[$i]['idalmacen'] . '" class="btnEditarAlmacen"><img src="/imagenes/iconos/editar.gif"></a>' .
            '<a href="/almacen/eliminar/"' . $data[$i]['idalmacen'] . '" class="btnEliminarAlmacen"><img src="/imagenes/iconos/eliminar.gif"></a>';
            echo "</td>";
            echo "</tr>";
            echo "</tr>";
        }
    }

    function contarNoRegistrado() {
        $ordenCompra = new Ordencompra();
        $cantidadOrdenCompra = $ordenCompra->contarOrdenCompraNoRegistrado();
        echo '{"cantidad":"' . $cantidadOrdenCompra . '"}';
    }

    function registra() {
        /* Pasos a realizar */
        /* 1.- Cabeceras: Se registra el movimiento y se actualiza la orden de compra */
        $dataMovimiento = $_REQUEST['Movimiento'];
        $dataMovimiento['idtipooperacion'] = 2;
        $dataMovimiento['iddocumentotipo'] = $dataMovimiento['tipodoc'];
        $dataMovimiento['serie'] = $dataMovimiento['serie'];
        if ($dataMovimiento['serie'] != "" && $dataMovimiento['ndocumento'] != "") {
            $dataMovimiento['essunat'] = 1;
        }
        $idOrdenCompra = $dataMovimiento['idordencompra'];
        $movimiento = new Movimiento();
        $exito1 = $movimiento->grabaMovimiento($dataMovimiento);
        $ordenCompra = new Ordencompra();
        //Tipo de Cambio Vigente:
        /* $tc=New TipoCambio();
          $tcv=$tc->consultavigente(2);
          $tipocambio=$tcv[0]['venta']; */
        //Tipo Cambio de la Orden:
        $tcv = $ordenCompra->TipoCambioxIdOrdenCompra($idOrdenCompra);
        $tipocambio = $tcv[0]['tipocambiovigente'];
        // echo $tipocambio;
        // exit;
        $oc['registrado'] = 1;
        $exito2 = $ordenCompra->actualizaOrdenCompra($oc, $idOrdenCompra);

        /* 2.- Detalle: Se registra el detalle del movimiento y actualiza el detalle de la orden */
        $dataDetalleMovimiento = $_REQUEST['Detallemovimiento'];
        $detalleMovimiento = new Detallemovimiento();
        $detalleOrdenCompra = new Detalleordencompra();

        /* 3.- Actualizando la tabla de productos y su historial */
        $dataProducto = $_REQUEST['Producto'];
        $producto = new Producto(); // Para actualizar los Stocks y precio valorizado
        $historialProducto = new Historialproducto(); // Para grabar precios de productos
        //Verificando que se grabaron las cabeceras
        if ($exito1 and $exito2) {
            //if(1==1){
            $items = count($dataDetalleMovimiento);
            for ($i = 1; $i <= $items; $i++) {
                //Definiendo datos a grabar:
                $iddetalleordencompra = $dataDetalleMovimiento[$i]['iddetalleordencompra'];
                $idProducto = $dataDetalleMovimiento[$i]['idproducto'];
                //$precioCosto=$dataDetalleMovimiento[$i]['preciocosto'];
                $precioCosto = $tipocambio * $dataDetalleMovimiento[$i]['preciocosto'];
                $cantidadRecibida = $dataDetalleMovimiento[$i]['cantidadrecibidaoc'];
                $cantidadsolicitada = $dataDetalleMovimiento[$i]['cantidadsolicitadaoc'];
                $importe = round($precioCosto * $cantidadRecibida, 2);
                $stockactual = $dataProducto[$i]['stockactual'];
                $stockdisponible = $dataProducto[$i]['stockdisponible'];
                $precioactual = $dataProducto[$i]['precioactual'];
                $stockproducto = $stockactual + $cantidadRecibida;
                $stockDisp = $stockdisponible + $cantidadRecibida - $cantidadsolicitada;
                //$stockdisponible=$stockproducto+$cantidadRecibida;
                $fecha = date("Y/m/d");

                //Actualizar la Orden de Compra, con la cantidad recibida.
                $doc['cantidadrecibidaoc'] = $cantidadRecibida;
                $exito3 = $detalleOrdenCompra->actualizaDetalleOrdenCompra($doc, $iddetalleordencompra);

                //DetalleMovimiento
                $ddm['idmovimiento'] = $exito1;
                $ddm['idproducto'] = $idProducto;
                $ddm['pu'] = $precioCosto;
                $ddm['cantidad'] = $cantidadRecibida;
                $ddm['importe'] = $importe;
                $ddm['stockactual'] = $stockproducto;
                //Valorizando el producto:
                $preciovalorizado = ($precioCosto * $cantidadRecibida + $stockactual * $precioactual) / $stockproducto;
                $preciovalorizado = round($preciovalorizado, 2);
                $ddm['preciovalorizado'] = $preciovalorizado;
                $exito4 = $detalleMovimiento->grabaDetalleMovimieto($ddm);

                //Actualizando datos del producto:

                $dp['stockactual'] = $stockproducto;
                $dp['stockdisponible'] = $stockDisp;
                $dp['esagotado'] = 0;
                if ($_REQUEST['vbimportaciones'] == 1) {
                    $dp['preciocosto'] = $preciovalorizado;
                }

                //$dp['stockdisponible']=$stockdisponible;
                $exito5 = $producto->actualizaProducto($dp, $idProducto);

                //Creando el historial del Producto
                $dhp['idordencompra'] = $idOrdenCompra;
                $dhp['idproducto'] = $idProducto;
                $dhp['fentrada'] = date("Y/m/d");
                $dhp['cantidadentrada'] = $cantidadRecibida;
                $dhp['stockactual'] = $stockproducto;
                $exito6 = $historialProducto->grabaHistorialProducto($dhp);
            }
            if ($exito3 and $exito4 and $exito5 and $exito6) {
                $ruta['ruta'] = "/almacen/regordencompra";
                $this->view->show("ruteador.phtml", $ruta);
            }
        }
    }

    function detalle() {
        $id = $_REQUEST['id'];
        $detalle = new Detalleordencompra();
        $proveedor = new Proveedor();
        $dataProveedor = $proveedor->buscaProveedorxOdenCompra($id);
        $data = $detalle->listaDetalleOrdenCompra($id);   
        $ordenCompra = new Ordencompra();
        $dataJefedeLinea = $ordenCompra->buscarVendedorXOrdenCompra($id);
        $ordenCompraVendedor = new Ordencompravendedor();
        $dataColaboradores = $ordenCompraVendedor->buscarColaboradorXOC($id);
        $tamColaboradores = count($dataColaboradores);
        $temptblDetalleOrdenCompra = "";
        for ($i = 0; $i < count($data); $i++) {            
            if (!empty($data[$i]['imagen'])) {
                $imagen = "/imagenes/productos/" . $data[$i]['codigopa'] . "/" . $data[$i]['imagen'];
            } else {
                $imagen = '/public/imagenes/sinFoto.jpg';
            }
            $temptblDetalleOrdenCompra .= "<tr>";
            $temptblDetalleOrdenCompra .= "<td><img src='" . $imagen . "' width='50' height='40'></td>";
            $temptblDetalleOrdenCompra .= "<td>" . $data[$i]['codigopa'] . "</td>";
            $temptblDetalleOrdenCompra .= "<td>" . $data[$i]['nompro'] . "</td>";
            $temptblDetalleOrdenCompra .= "<td>" . $data[$i]['fobdoc'] . "</td>";
            $temptblDetalleOrdenCompra .= "<td>" . $data[$i]['cantidadsolicitadaoc'] . "</td>";
            $temptblDetalleOrdenCompra .= "<td>" . $data[$i]['cantidadrecibidaoc'] . "</td>";
            $temptblDetalleOrdenCompra .= "</tr>";
        }        
        $tblEncabezado .= '<tr>
                            <th style="width: 110px"' . ($tamColaboradores > 1 ? ' rowspan="' . $tamColaboradores . '"' : '') . '>Proveedor: </th>
                            <td' . ($tamColaboradores > 1 ? ' rowspan="' . $tamColaboradores . '"' : '') . '>' . $dataProveedor[0]['razonsocialp'] . '</td>';
        if (count($dataJefedeLinea) > 0) {
            $tblEncabezado .= ' <th style="width: 130px"' . ($tamColaboradores > 1 ? ' rowspan="' . $tamColaboradores . '"' : '') . '>Jefe de Linea: </th>
                                <td' . ($tamColaboradores > 1 ? ' rowspan="' . $tamColaboradores . '"' : '') . '>' . $dataJefedeLinea[0]['nombres'] . ' ' . $dataJefedeLinea[0]['apellidopaterno'] . ' ' . $dataJefedeLinea[0]['apellidomaterno'] . '</td>';
        }
        if ($tamColaboradores > 0) {
            $tblEncabezado .= '<th style="width: 130px"' . ($tamColaboradores > 1 ? ' rowspan="' . $tamColaboradores . '"' : '') . '>Colaboradores: </th>';
            for ($i = 0; $i < $tamColaboradores; $i++) {
                if ($i > 0) {
                    $tblEncabezado .= '<tr>';
                }
                $tblEncabezado .= "<td>" . $dataColaboradores[$i]['nombres'] . " " . $dataColaboradores[$i]['apellidopaterno'] . " " . $dataColaboradores[$i]['apellidomaterno'] . "</td></tr>";
            }
        } else {
            $tblEncabezado .= '</tr>';
        }
        $dataJson['tblEncabezado'] = $tblEncabezado;
        $dataJson['tblDetalleOrdenCompra'] = $temptblDetalleOrdenCompra;
        echo json_encode($dataJson);
    }

    function confirmar() {
        $id = $_REQUEST['idOrdenCompra'];
        $dataOrdenCompra = $_REQUEST['OrdenCompra'];
        $dataOrdenCompraDetalle = $_REQUEST['Detalleordencompra'];
        //echo '<pre>';
        //print_r($dataOrdenCompraDetalle);
        //exit;
        $dataOrdenCompra['valorizado'] = 1;
        if ($_REQUEST['conformidad'] != 'on') {

            $dataOrdenCompra['vbimportaciones'] = 0;
        }
        if ($_REQUEST['registrado'] == 1) {
            $detalleMovimiento = $this->AutoLoadModel('detallemovimiento');
            $dataproducto = $_REQUEST['Producto'];
        }

        $totalDOC = count($dataOrdenCompraDetalle);
        $ordenCompra = new Ordencompra();
        $detalleOrdenCompra = new Detalleordencompra();
        $producto = new Producto();
        $historialProducto = new Historialproducto();
        $exito1 = $ordenCompra->actualizaOrdenCompra($dataOrdenCompra, $id);

        for ($i = 1; $i <= $totalDOC; $i++) {
            //Actualizando el DetalleOrdenCompra
            if (!isset($dataOrdenCompraDetalle[$i]['cifunitario'])) {
                $dataOrdenCompraDetalle[$i]['cifunitario'] = ($dataOrdenCompraDetalle[$i]['cantidadrecibidaoc'] > 0 ? $dataOrdenCompraDetalle[$i]['ciftotal']/$dataOrdenCompraDetalle[$i]['cantidadrecibidaoc'] : 0 );
            }
            $idDOC = $dataOrdenCompraDetalle[$i]['iddetalleordencompra'];
            $ddoci = $dataOrdenCompraDetalle[$i];
            $exito_doc = $detalleOrdenCompra->actualizaDetalleOrdenCompra($ddoci, $idDOC);
            $idProducto = $dataOrdenCompraDetalle[$i]['idproducto'];

            if ($_REQUEST['registrado'] == 1) {
                $tcv = $ordenCompra->TipoCambioxIdOrdenCompra($id);
                $tipocambio = $tcv[0]['tipocambiovigente'];

                $filtro = "m.idordencompra='$id' and dm.idproducto='$idProducto' ";
                $dataMovimiento = $detalleMovimiento->buscaDetalleMovimientoxFiltro($filtro);
                $iddetallemovimiento = $dataMovimiento[0]['iddetallemovimiento'];
                $precioCosto = ($dataOrdenCompraDetalle[$i]['cifunitario']) * ($tipocambio);
                $cantidadRecibida = $dataOrdenCompraDetalle[$i]['cantidadrecibidaoc'];
                $stockactual = $dataMovimiento[0]['stockactual'] - $dataMovimiento[0]['cantidad'];
                $precioactual = $dataproducto[$i]['preciocosto'];
                $stockproducto = $stockactual + $cantidadRecibida;


                $preciovalorizado = ($precioCosto * $cantidadRecibida + $stockactual * $precioactual) / $stockproducto;
                $preciovalorizado = round($preciovalorizado, 2);
                $ddm['preciovalorizado'] = $preciovalorizado;
                $ddm['pu'] = $precioCosto;
                $datop['preciocosto'] = $preciovalorizado;
                
                $exitoM = $detalleMovimiento->actualizaDetalleMovimientoxid($iddetallemovimiento, $ddm);
            }
            $datop['fob'] = $dataOrdenCompraDetalle[$i]['fobdoc'];
            $exitoP = $producto->actualizaProducto($datop, $idProducto);
        }
        if ($exito_doc and $exito1) {
            $ruta['ruta'] = "/importaciones/ordencompra";
            $this->view->show('ruteador.phtml', $ruta);
        }
    }

    function cuadroUtilidad() {
        $ordenCompra = $this->AutoLoadModel('ordencompra');
        $id = $_REQUEST['id'];
        $data['valorizado'] = $ordenCompra->OrdenesValorizados(" and fordencompra>='" . date('Y') . "-01-01' and fordencompra<='" . date('Y') . "-12-31'");
        if (!empty($id)) {
            $porcifventas = $this->configIni('Parametros', 'PorCifVentas');
            $detalleOrdenCompra = $this->AutoLoadModel('detalleordencompra');
            $detalleOrdenVenta = $this->AutoLoadModel('detalleordenventa');
            $dataOrdenCompra = $ordenCompra->OrdenCuadroUtilidad($id);
            if (count($dataOrdenCompra) > 0) {
                $data['Ordencompra'] = $dataOrdenCompra;
                if ($dataOrdenCompra[0]['idcuadroutilidad'] > 0) {
                    $detallecuadroutilidad = $this->AutoLoadModel('detallecuadroutilidad');
                    $dataDetalleordencompra = $detallecuadroutilidad->listarXidcuadroutilidad($dataOrdenCompra[0]['idcuadroutilidad'], $id);
                } else {
                    $dataDetalleordencompra = $detalleOrdenCompra->listaDetalleOrdenCompra($id);
                }
                $tipocambio = $dataOrdenCompra[0]['tipocambiovigente'];
                $idtipocambio = $dataOrdenCompra[0]['idtipocambiovigente'];
                $cantidad = count($dataDetalleordencompra);
                $porcentaje = (($porcifventas + 100) / 100);
                for ($i = 0; $i < $cantidad; $i++) {
                    if ($detalleOrdenVenta->verificarVentadelproducto($dataDetalleordencompra[$i]['idproducto']) == 0 && $dataOrdenCompra[0]['cuadroutilidad'] != 1) {
                        $tempProducto = '<b id="span_' . $dataDetalleordencompra[$i]['idproducto'] . '">' . $dataDetalleordencompra[$i]['nompro'] . '</b>' .
                                '<input class="text-250 inputText" type="text" data-id="' . $dataDetalleordencompra[$i]['idproducto'] . '" value="' . $dataDetalleordencompra[$i]['nompro'] . '">' .
                                '<a href="#" class="GuardarNompro" data-id="' . $dataDetalleordencompra[$i]['idproducto'] . '"><img src="/imagenes/grabar.gif" width="17px"></a>' .
                                '<a href="#" class="EditarNompro" data-id="' . $dataDetalleordencompra[$i]['idproducto'] . '"><img src="/imagenes/iconos/editar.gif" width="17px"></a>';
                    } else {
                        $tempProducto = '<span>' . $dataDetalleordencompra[$i]['nompro'] . '</span>';
                    }
                    $contenidoTbl .= '<tr>' .
                            '<td>' . ($i + 1) .
                            '<input type="hidden" value="' . $dataDetalleordencompra[$i]['idproducto'] . '" name="idproducto[' . $i . ']">' .
                            '<input type="hidden" value="' . $dataDetalleordencompra[$i]['iddetalleordencompra'] . '" name="iddetalleordencompra[' . $i . ']">' .
                            '</td>' .
                            '<td>' . $dataDetalleordencompra[$i]['codigopa'] . '</td>' .
                            '<td class="text-300">' . $tempProducto . '</td>' .
                            '<td>' . $dataDetalleordencompra[$i]['marca'] . '</td>' .
                            '<td><input type="hidden" value="' . $dataDetalleordencompra[$i]['cantidadrecibidaoc'] . '" name="Producto[' . $i . '][cantidadrecibidaoc]">' .
                            $dataDetalleordencompra[$i]['cantidadrecibidaoc'] . '</td>' .
                            '<td>' . $dataDetalleordencompra[$i]['unidadmedida'] . '</td>' .
                            '<td>' . $dataDetalleordencompra[$i]['piezas'] . '</td>' .
                            '<td>' . $dataDetalleordencompra[$i]['carton'] . '</td>' .
                            '<td>' . number_format($dataDetalleordencompra[$i]['fobdoc'], 2) . '<input type="hidden" name="Producto[' . $i . '][fobUnit]" class="fobUnit" value="' . $dataDetalleordencompra[$i]['fobdoc'] . '"></td>';
                    /* '<td>' . number_format($dataDetalleordencompra[$i]['preciocosto'] / $tipocambio, 2) . 
                      '<a href="#" class="EditarPrecioCosto" data-id="' . $dataDetalleordencompra[$i]['idproducto'] . '"><img src="/imagenes/iconos/editar.gif" width="17px"></a>' .
                      '</td>'; */
                    $cifv = round($dataDetalleordencompra[$i]['fobdoc'] * $porcentaje, 2) == '0.00' ? 0.01 : round($dataDetalleordencompra[$i]['fobdoc'] * $porcentaje, 2);
                    $contenidoTbl .= '<td><input type="text"  name="Producto[' . $i . '][cifventasdolares]" value="' . $cifv . '" class="cifVentas" style="border:none" readonly size="5"></td>';
                    $cifvs = round($dataDetalleordencompra[$i]['fobdoc'] * $porcentaje * $tipocambio, 2) == '0.00' ? 0.01 : round($dataDetalleordencompra[$i]['fobdoc'] * $porcentaje * $tipocambio, 2);
                    $contenidoTbl .= '<input type="hidden" name="Producto[' . $i . '][cifventas]" value="' . round($cifvs * $tipocambio, 2) . '">' .
                            '<td><input type="text" class="tipocambio" size="4" name="Producto[' . $i . '][valortipocambio]" value="' . round($tipocambio, 2) . '"></td>' .
                            '<td><input type="text"' . ($dataOrdenCompra[0]['idcuadroutilidad'] > 0 ? : ' class="neto"') . ' size="5" name="Producto[' . $i . '][preciotopedolares]" value="' . ($dataOrdenCompra[0]['idcuadroutilidad'] > 0 ? number_format($dataDetalleordencompra[$i]['preciotopedolares'], 2) : '') . '"><label style="display:none;" class="lblNeto"></label></td>' .
                            '<td><input type="text" size="5"' . ($dataOrdenCompra[0]['idcuadroutilidad'] > 0 ? : ' class="preciolista"') . ' name="Producto[' . $i . '][preciolistadolares]" readonly style="background:skyblue;" value="' . ($dataOrdenCompra[0]['idcuadroutilidad'] > 0 ? number_format($dataDetalleordencompra[$i]['preciolistadolares'], 2) : '0.00') . '"><label style="display:none;" class="lblPrecioListaDolares"></label></td>' .
                            '<td><input type="text"' . ($dataOrdenCompra[0]['idcuadroutilidad'] > 0 ? : ' class="netosoles"') . ' size="5" name="Producto[' . $i . '][preciotope]" readonly style="background:lightblue;" value="' . ($dataOrdenCompra[0]['idcuadroutilidad'] > 0 ? number_format($dataDetalleordencompra[$i]['preciotope'], 2) : '0.00') . '"><label style="display:none;" class="lblPrecioNetoSoles"></label></td>' .
                            '<td><input type="text" size="5"' . ($dataOrdenCompra[0]['idcuadroutilidad'] > 0 ? : ' class="preciolistasoles"') . ' name="Producto[' . $i . '][preciolista]" readonly style="background:lightblue;" value="' . ($dataOrdenCompra[0]['idcuadroutilidad'] > 0 ? number_format($dataDetalleordencompra[$i]['preciolista'], 2) : '0.00') . '"><label style="display:none;" class="lblPrecioListaSoles"></label></td>' .
                            '<td><input type="text" size="5"' . ($dataOrdenCompra[0]['idcuadroutilidad'] > 0 ? : ' class="utilidad"') .' readonly value="' . ($dataOrdenCompra[0]['idcuadroutilidad'] > 0 ? number_format($dataDetalleordencompra[$i]['utilidadDetalle'], 2) : '0.00') . '" style="border:none;text-align:center;" name="DetalleOrdenCompra[' . $i . '][utilidadDetalle]"><label style="display:none;" class="lblUtilidad"></td>';
                    $contenidoTbl .= '</tr>';
                }
            }
        }
        $data['contenidoTbl'] = $contenidoTbl;
        $this->view->show('/ordencompra/cuadroUtilidad.phtml', $data);
    }
    
    function actualizarnompro() {
        $idproducto = $_REQUEST['idproducto'];
        $nompro = $_REQUEST['nompro'];
        $detalleOrdenVenta = $this->AutoLoadModel('detalleordenventa');
        if ($detalleOrdenVenta->verificarVentadelproducto($idproducto) == 0) {
            $producto = $this->AutoLoadModel('producto');
            $data['nompro'] = $nompro;
            $producto->actualizaProducto($data, $idproducto);
            $dataRespuesta['rspta'] = 1;
        } else {
            $dataRespuesta['rspta'] = 0;
        }        
        echo json_encode($dataRespuesta);
    } 

    function actualizaUtilidad() {
        $producto = $this->AutoLoadModel('producto');
        $detalleordencompra = $this->AutoLoadModel('detalleordencompra');
        $ordencompra = $this->AutoLoadModel('ordencompra');
        $cuadroutilidad = $this->AutoLoadModel('cuadroutilidad');
        $detallecuadroutilidad = $this->AutoLoadModel('detallecuadroutilidad');
        $dataProducto = $_REQUEST['Producto'];
        $idProducto = $_REQUEST['idproducto'];
        $iddetalleordencompra = $_REQUEST['iddetalleordencompra'];
        $utilidadTotal = 0;
        $tipocambio = 1;

        $dataDetalleOrdenCompra = $_REQUEST['DetalleOrdenCompra'];
        $idordencompra = $_REQUEST['idordencompra'];
        $cantidadProducto = count($dataProducto);
        if (!empty($idordencompra)&&$cantidadProducto>0) {
            $dataCU['idordencompra'] = $idordencompra;
            $idcuadroutilidad = $cuadroutilidad->graba($dataCU);
            $dataDetCU['idcuadroutilidad'] = $idcuadroutilidad;
        }
        for ($i = 0; $i < $cantidadProducto; $i++) {
            $utilidadTotal += $dataDetalleOrdenCompra[$i]['utilidadDetalle'];
            $dProducto['cifventas'] = $dataProducto[$i]['cifventas'];
            $dProducto['cifventasdolares'] = $dataProducto[$i]['cifventasdolares'];
            $dProducto['preciotope'] = $dataProducto[$i]['preciotope'];
            $dProducto['preciotopedolares'] = $dataProducto[$i]['preciotopedolares'];
            $dProducto['preciolista'] = round($dataProducto[$i]['preciolista'] * $tipocambio, 2);
            $dProducto['preciolistadolares'] = round($dataProducto[$i]['preciolistadolares'] * $tipocambio, 2);
            $dProducto['valortipocambio'] = $dataProducto[$i]['valortipocambio'];

            $exito = $producto->actualizaProducto($dProducto, $idProducto[$i]);
            if ($exito) {
                $dDetalleOrdenCompra['utilidadDetalle'] = $dataDetalleOrdenCompra[$i]['utilidadDetalle'];
                $dDetalleOrdenCompra['precio_lista'] = $dataProducto[$i]['preciolista'];
                $dDetalleOrdenCompra['precio_tope'] = $dataProducto[$i]['preciotope'];
                $dDetalleOrdenCompra['precio_listadolares'] = $dataProducto[$i]['preciolistadolares'];
                $dDetalleOrdenCompra['precio_topedolares'] = $dataProducto[$i]['preciotopedolares'];
                $exito2 = $detalleordencompra->actualizaDetalleOrdenCompra($dDetalleOrdenCompra, $iddetalleordencompra[$i]);
                $dataDetCU['idproducto'] = $idProducto[$i];
                $dataDetCU['cantidad'] = $dataProducto[$i]['cantidadrecibidaoc'];
                $dataDetCU['fobunitariodolares'] = $dataProducto[$i]['fobUnit'];
                $dataDetCU['cifventas'] = $dataProducto[$i]['cifventas'];
                $dataDetCU['cifventasdolares'] = $dataProducto[$i]['cifventasdolares'];
                $dataDetCU['preciocosto'] = $dataProducto[$i]['preciotopedolares'];
                $dataDetCU['preciotope'] = $dataProducto[$i]['preciotope'];
                $dataDetCU['preciotopedolares'] = $dataProducto[$i]['preciotopedolares'];
                $dataDetCU['preciolista'] = round($dataProducto[$i]['preciolista'] * $tipocambio, 2);
                $dataDetCU['preciolistadolares'] = round($dataProducto[$i]['preciolistadolares'] * $tipocambio, 2);
                $dataDetCU['valortipocambio'] = $dataProducto[$i]['valortipocambio'];
                $dataDetCU['utilidadDetalle'] = $dataDetalleOrdenCompra[$i]['utilidadDetalle'];
                $dataCU['valortipocambio'] = $dataProducto[$i]['valortipocambio'];
                $detallecuadroutilidad->graba($dataDetCU);
            }
        }
        if ($exito && $exito2) {
            $dataCU['totalutilidad'] = $utilidadTotal;
            $cuadroutilidad->actualiza($dataCU, $idcuadroutilidad);
            $data['idcuadroutilidad'] = $idcuadroutilidad;
            $data['utilidad'] = $utilidadTotal;
            $data['cuadroutilidad'] = 1;
            $exito3 = $ordencompra->actualizaOrdenCompra($data, $idordencompra);
            if ($exito3) {
                $ruta['ruta'] = "/ordencompra/cuadroUtilidad";
                $this->view->show('ruteador.phtml', $ruta);
            }
        }
    }
    
    function ordenesconfirmadasxanio() {
        $anio = $_REQUEST['id'];
        $ordenCompra = $this->AutoLoadModel('ordencompra');
        $valorizados = $ordenCompra->OrdenesValorizados(" and fordencompra>='" . $anio . "-01-01' and fordencompra<='" . $anio . "-12-31'");
        $cantidadValorizado = count($valorizados);
        echo '<option>-- Seleccione --</option>';
        for ($i = $cantidadValorizado - 1; $i >= 0; $i--) { 
            echo '<option value="' . $valorizados[$i]['idordencompra'] . '">' . $valorizados[$i]['codigooc'] . '</option>';
        }
    }

    function vistaRespuesta() {
        $data['codigooc'] = $_REQUEST['id'];
        if ($_REQUEST['id']) {
            $this->view->show("/ordencompra/vistaRespuesta.phtml", $data);
        } else {
            $this->view->show("/index/index.phtml", $data);
        }
    }

    function autoCompleteAprobados() {
        $tex = $_REQUEST['term'];
        $ordenCompra = $this->AutoLoadModel('ordencompra');
        $data = $ordenCompra->autoCompleteAprobados($tex);
        echo json_encode($data);
    }

    function utilidadxContainer() {
        $ordenCompra = $this->AutoLoadModel('ordencompra');
        $year = $_REQUEST['id'];
        $data['valorizado'] = $ordenCompra->fechaxOrdenes();
        if (!empty($year)) {
            $data['porcifventas'] = $this->configIni('Parametros', 'PorCifVentas');
            $detalleOrdenCompra = $this->AutoLoadModel('detalleordencompra');
            $dataOrdenCompra = $ordenCompra->ListaCuadroUtilidad($year);
            $data['Detalle'] = $detalleOrdenCompra->listaDetalleOrdenCompraxFecha($year);
            $data['Ordencompra'] = $dataOrdenCompra;
        }
        $this->view->show('/ordencompra/utilidadxContainer.phtml', $data);
    }

    function listaDetalle() {
        $id = $_REQUEST['idcontenedor'];
        $data['porcifventas'] = $this->configIni('Parametros', 'PorCifVentas');
        $ordenCompra = $this->AutoLoadModel('ordencompra');
        $reporte = $this->AutoLoadModel('reporte');
        $detalleOrdenCompra = $this->AutoLoadModel('detalleordencompra');

        $dataOrdenCompra = $ordenCompra->OrdenCuadroUtilidad($id);
        $dataDetalleordenCompra = $detalleOrdenCompra->listaDetalleOrdenCompra($id);
        $cantidad = count($dataDetalleordenCompra);
        $tipocambio = $dataOrdenCompra[0]['tipocambiovigente'];
        $porcentaje = (($data['porcifventas'] + 100) / 100);
        $totalUtilidad = 0;
        $utilidadDolares = 0;
        $utilidadDolaresxProducto = 0;

        for ($i = 0; $i < $cantidad; $i++) {
            $cont = 0;
            $salidas = 0;
            $entradas = 0;
            $productos = $reporte->reporteKardexProduccion("", "", $dataDetalleordenCompra[$i]['idproducto'], "", "");
            for ($x = 0; $x < count($productos); $x++) {
                if ($productos[$x]['idordencompra'] == $id) {
                    //$idmovimiento=$datos[$i]['codigooc'];
                    $a = $x + 1;
                    break;
                }
            }
            for ($y = $a; $y < count($productos); $y++) {
                if ($productos[$y]['codigooc'] == "" and $productos[$y]['codigov'] != "") {
                    $cont++;
                    if ($productos[$y]['tipo movimiento'] == "Salidas") {
                        $salidas += $productos[$y]['cantidad'];
                    } else {
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

            echo "<tr>";
            echo"<td>";
            echo ($i + 1);
            echo"</td>";
            echo "<td>" . $dataDetalleordenCompra[$i]['codigopa'] . "</td>";
            echo"<td class='text-300'>" . $dataDetalleordenCompra[$i]['nompro'] . "</td>";
            echo"<td>" . $dataDetalleordenCompra[$i]['marca'];
            "</td>";
            echo"<td>" . $dataDetalleordenCompra[$i]['cantidadrecibidaoc'] . "</td>";
            echo"<td>" . $dataDetalleordenCompra[$i]['unidadmedida'] . "</td>";
            echo"<td>" . $dataDetalleordenCompra[$i]['piezas'] . "</td>";
            echo"<td>" . $dataDetalleordenCompra[$i]['carton'] . "</td>";

            echo"<td>" . number_format($dataDetalleordenCompra[$i]['preciocosto'] / $tipocambio, 2) . "</td>";
            echo"<td>" . number_format($dataDetalleordenCompra[$i]['fobdoc'] * $porcentaje, 2) . "</td>";
            echo"<td>" . number_format($tipocambio, 2) . "</td>";
            echo"<td>" . $dataDetalleordenCompra[$i][preciotopedolares] . "</td>";
            echo"<td>" . number_format($preciolistaDolares, 2) . "</td>";
            echo"<td>" . number_format($dataDetalleordenCompra[$i]['preciotope'], 2) . "</td>";
            echo"<td>" . number_format($dataDetalleordenCompra[$i]['preciolista'], 2) . "</td>";
            echo"<td>" . $productosVendidos . "</td>";
            echo"<td>" . number_format($utilidadReal, 1) . "%" . "</td>";
            echo"<td>" . number_format($precioVenta, 2) . "</td>";
            echo"<td>" . number_format($utilidadDolaresxProducto, 2) . "</td>";
            echo "</tr>";
        }
        echo"<tr><td  colspan='18'></td></tr>";
        echo"<tr><td  colspan='14'></td><td></td><th colspan='2'>TOTAL</th><td  colspan='2' style='background:#fafafa;font-weight:bold;font-size:15;'>" . "US $ " . number_format($utilidadTotal, 2) . "</td></tr>";
    }

    function listarordenes() {
        $texIni = $_REQUEST['term'];
        $ordenCompra = new OrdenCompra();
        $data = $ordenCompra->listarordenes($texIni);
        echo json_encode($data);
    }

    function reporteDetalleOrdenCompra() {
        $ordencompra = new Ordencompra();
        $data = $ordencompra->reporteDetalleOrdenCompra($_POST['txtid']);
        $cantidad = count($data);

        $fobtotal = 0;
        $totalventa = 0;
        $totalcontado = 0;
        $totalcredito = 0;
        $totalstock = 0;

        for ($i = 0; $i < $cantidad; $i++) {

            echo "<tr>";
            echo "<td>" . ($i + 1) . "</td>";
            echo "<td>" . $data[$i]['codigopa'] . "</td>";
            echo "<td>" . $data[$i]['cantidadrecibidaoc'] . "</td>";
            echo "<td>" . $data[$i]['medida'] . "</td>";
            echo "<td>$. " . $data[$i]['fobdoc'] . "</td>";
            $fobtotal = $fobtotal + $data[$i]['fobtotal'];
            echo "<td>$. " . number_format($data[$i]['fobtotal'], 2) . "</td>";
            $data2 = $ordencompra->reporteDetalleOrdenCompra2($data[$i]['idproducto'], $data[$i]['fecha']);
            echo "<td style='background: #DCD9D9;'>" . $data2[0]['vendida'] . "</td>";
            echo "<td style='background: #DCD9D9;'>PCS</td>";
            echo "<td style='background: #DCD9D9;'>" . number_format($data2[0]['preciototal'], 2) . "</td>";
            $totalventa = $totalventa + ($data2[0]['preciototal'] * $data2[0]['vendida']);
            echo "<td style='background: #DCD9D9;'>$. " . number_format($data2[0]['preciototal'] * $data2[0]['vendida'], 2) . "</td>";
            echo "<td style='background: #92ABF3;'>" . $data2[0]['contado'] . "</td>";
            $totalcontado = $totalcontado + ($data2[0]['preciototal'] * $data2[0]['contado']);
            echo "<td style='background: #92ABF3;'>$. " . number_format($data2[0]['preciototal'] * $data2[0]['contado'], 2) . "</td>";
            echo "<td style='background: #59D88E;'>" . $data2[0]['credito'] . "</td>";
            $totalcredito = $totalcredito + ($data2[0]['preciototal'] * $data2[0]['credito']);
            echo "<td style='background: #59D88E;'>$. " . number_format($data2[0]['preciototal'] * $data2[0]['credito'], 2) . "</td>";
            if ($data[$i]['stockactual'] == 0) {
                echo "<td>0.00</td>";
                echo "<td>0.00</td>";
            } else {
                echo "<td style='background: #FFD600;'>" . $data[$i]['stockactual'] . "</td>";
                $totalstock = $totalstock + ($data2[0]['preciototal'] * $data[$i]['stockactual']);
                echo "<td style='background: #FFD600;'>$. " . number_format($data2[0]['preciototal'] * $data[$i]['stockactual'], 2) . "</td>";
            }
            echo "</tr>";
        }

        echo "<tr>";
        echo "<td colspan='5'></td>";
        echo "<th>$. " . number_format($fobtotal, 2) . "</th>";
        echo "<td colspan='3'></td>";
        echo "<td style='background:#C1C1C1'>$. " . number_format($totalventa, 2) . "</td>";
        echo "<td></td>";
        echo "<td style='background:#6C91FB'>$. " . number_format($totalcontado, 2) . "</td>";
        echo "<td></td>";
        echo "<td style='background:#0AC557'>$. " . number_format($totalcredito, 2) . "</td>";
        echo "<td></td>";
        echo "<td style='background:#C1C1C1'>$. " . number_format($totalstock, 2) . "</td>";
        echo "</tr>";
    }

    function actualizado() {
        $id = $_REQUEST['id'];
        if (!empty($_REQUEST['id']) && $_REQUEST['id'] > 0) {
            $ordenCompraModel = new Ordencompra();
            $Ordencompra = $ordenCompraModel->editaOrdenCompra($id);
            if ($Ordencompra[0]['valorizado'] == 1) {
                if ($Ordencompra[0]['actualizado'] == 0) {
                    $detalleOrdenCompra = new Detalleordencompra();
                    $data['Detalleordencompra'] = $detalleOrdenCompra->listaDetalleOrdenCompra($id);
                } else {
                    $estructuraCostos = New Estructuradecostos();
                    $data['lstOrdeneDeCompra'] = $ordenCompraModel->listarOrdenCompraXDua($Ordencompra[0]['idestructuradecostos']);
                    $data['dataEDC'] = $estructuraCostos->verEstructuraCostos($Ordencompra[0]['idestructuradecostos']);
                    $data['Detalleordencompra'] = $estructuraCostos->listadetallexestructuradecostos($Ordencompra[0]['idestructuradecostos']);
                    $Ordencompra[0]['tipocambiovigente'] = $data['dataEDC'][0]['tipocambiovigente'];
                }
                $data['Ordencompra'] = $Ordencompra;
                $almacen = new Almacen();
                $proveedor = new Proveedor();
                $empresa = $this->AutoLoadModel('empresa');
                $rutaImagen = $this->rutaImagenesProducto();

                $data['Empresa'] = $almacen->listadoAlmacen();
                $data['RutaImagen'] = $rutaImagen;

                $data['Proveedor'] = $proveedor->listadoProveedores();
                $data['Flete'] = $empresa->listadoEmpresaxIdTipoEmpresa(1);
                $data['Aduanas'] = $empresa->listadoEmpresaxIdTipoEmpresa(3);
                $data['Seguro'] = $empresa->listadoEmpresaxIdTipoEmpresa(2);
                if ($Ordencompra[0]['nuevoformato'] == 0) {
                    $this->view->show("/ordencompra/actualizarcompra.phtml", $data);
                } else {
                    $this->view->show("/ordencompra/actualizarcompranuevo.phtml", $data);
                }                
            } else {
                $ruta['ruta'] = "/importaciones/ordencompra";
                $this->view->show("ruteador.phtml", $ruta);
            }
        } else {
            $ruta['ruta'] = "/importaciones/ordencompra";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function guardardua() {
        $idordencompra = $_REQUEST['idOrdenCompra'];
        $data1['serieDua'] = $_REQUEST['serieDua'];
        $data1['nroDua'] = $_REQUEST['nroDua'];
        $data1['fechaCompraOC'] = $_REQUEST['fechaCompraDua'];
        $ordenCompra = new Ordencompra();
        $exito = $ordenCompra->actualizaOrdenCompra($data1, $idordencompra);
        $resp['msj'] = 1;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function autoCompleteCompraXDua() {
        $tex = $_REQUEST['term'];
        $ordenCompra = $this->AutoLoadModel('ordencompra');
        $data = $ordenCompra->autoCompleteAprobadosSinDua($tex);
        echo json_encode($data);
    }
    
    function autoCompleteCompraXDuaNuevo() {
        $tex = $_REQUEST['term'];
        $ordenCompra = $this->AutoLoadModel('ordencompra');
        $data = $ordenCompra->autoCompleteAprobadosSinDua($tex, 1);
        echo json_encode($data);
    }

    function listardetalleXoc() {
        $idordencompra = $_REQUEST['idordencompra'];
        $cantidadActual = $_REQUEST['cantidad'];
        $tablaContenido = "";
        if (!empty($idordencompra) && $idordencompra > 0) {
            $ordenCompra = new Ordencompra();
            $Ordencompra = $ordenCompra->editaOrdenCompra($idordencompra);
            if ($Ordencompra[0]['valorizado'] == 1) {
                $Detalleordencompra = array();
                if ($Ordencompra[0]['nuevoformato']==0 &&$Ordencompra[0]['actualizado'] == 0) {
                    $docmodel = new Detalleordencompra();
                    $Detalleordencompra = $docmodel->listaDetalleOrdenCompra($idordencompra);
                }
                $empresa = $this->AutoLoadModel('empresa');
                $rutaImagen = $this->rutaImagenesProducto();

                $tamDoc = count($Detalleordencompra);
                $importeTotalOC = 0;
                for ($i = 0; $i < $tamDoc; $i++) {
                    if (empty($Detalleordencompra[$i]['imagen'])) {
                        $rutaCompleta = '/public/imagenes/sinFoto.jpg';
                    } else {
                        $rutaCompleta = $RutaImagen . $Detalleordencompra[$i]['codigopa'] . '/' . $Detalleordencompra[$i]['imagen'];
                    }
                    $tamano = '30px';
                    $tamanoM = '55px';
                    $tamanoG = '70px';
                    $tamanoGG = "100px";
                    $tablaContenido .= '<tr class="ColDoc_' . $idordencompra . '">';
                    $tablaContenido .= '<td>' . ($i + 1 + $cantidadActual) . '</td>';
                    //Codigo,Cantidad,Volumen,Fob,Fob Total
                    $iddetalleOC = $Detalleordencompra[$i]['iddetalleordencompra'];
                    $idproductoOC = $Detalleordencompra[$i]['idproducto'];
                    $cantidad = $Detalleordencompra[$i]['cantidadrecibidaoc'];
                    $volumenxUnidad = $Detalleordencompra[$i]['vol'];
                    $volumen = $Detalleordencompra[$i]['cbm'];
                    $fob = $Detalleordencompra[$i]['fobdoc'];
                    $piezas = !empty($Detalleordencompra[$i]['piezas']) ? $Detalleordencompra[$i]['piezas'] : 0;
                    $carton = !empty($Detalleordencompra[$i]['carton']) ? $Detalleordencompra[$i]['carton'] : 0;
                    $fobTotal = $fob * $cantidad;
                    $tablaContenido .= '<td class="codigo" style="width:' . $tamanoGG . '"><a href="/producto/editar/' . $idproductoOC . '" target="_blank">' . $Detalleordencompra[$i]['codigopa'] . "</a></td>";
                    $tablaContenido .= '<input type="hidden" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][iddetalleordencompra]" value="' . $iddetalleOC . '">';
                    $tablaContenido .= '<input type="hidden" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][idproducto]" value="' . $idproductoOC . '">';
                    $tablaContenido .= '<td class="codigo" style="width:' . $tamanoGG . '">' . $Detalleordencompra[$i]['nompro'] . '</td>';
                    $tablaContenido .= '<td class="codigo" style="width:' . $tamanoGG . '">' . $Detalleordencompra[$i]['marca'] . '</td>';
                    $tablaContenido .= '<td class="center"><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][cantidadrecibidaoc]" class="txtCantidadDetalle numeric required" style="width:' . $tamano . ';color:red;" value="' . $cantidad . '"  ></td>';
                    $tablaContenido .= '<td class="codigo" style="width:' . $tamanoGG . '">' . $Detalleordencompra[$i]['unidadmedida'] . '</td>';
                    $tablaContenido .= '<input class="piezas" type="hidden" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][piezas]" value="' . $piezas . '"> <input type="hidden" name="Producto[' . ($i + 1 + $cantidadActual) . '][preciocosto]"  value="' . $Detalleordencompra[$i]['preciocosto'] . '">';
                    $tablaContenido .= '<input class="carton" type="hidden" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][carton]" value="' . $carton . '">';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][vol]" value="' . $volumenxUnidad . '" class="txtVolumen numeric required" style="width:' . $tamano . '" ></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][cbm]" value="' . number_format($volumen, 2, '.', '') . '" class="txtVolumenDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][fobdoc]" class="txtfobDetalle numeric required" style="width:' . $tamano . '" value="' . $fob . '"   ></td>';
                    $tablaContenido .= '<td><input type="text" value="' . number_format($fobTotal, 2, '.', '') . '"  class="txtfobTotalDetalle numeric required" style="width:' . $tamanoM . '" readonly ></td>';
                    //Flete,Seguro,Cif,Cif Unit.
                    $flete = !empty($Detalleordencompra[$i]['fleted']) ? ($Detalleordencompra[$i]['fleted']) : "0.00";
                    $seguro = !empty($Detalleordencompra[$i]['seguro']) ? ($Detalleordencompra[$i]['seguro']) : "0.00";
                    $ciftotal = $fobTotal + $seguro + $flete;
                    $cifunitario = $ciftotal / $cantidad;
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][fleted]" value="' . $flete . '" class="txtFleteDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][seguro]" value="' . $seguro . '" class="txtSeguroDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][ciftotal]" value="' . number_format($ciftotal, 2, '.', '') . '" class="txtciftotal required" style="width:' . $tamanoM . '" readonly ></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][cifunitario]" value="' . number_format($cifunitario, 2, '.', '') . '" class="txtcifunitario numeric required" style="width:' . $tamano . '" readonly ></td>';
                    //%AdValorem,AdValorem,Tasa Desapacho
                    $advaloremporcentaje = !empty($Detalleordencompra[$i]['advaloremporcentaje']) ? ($Detalleordencompra[$i]['advaloremporcentaje']) : "0";
                    $advaloremvalor = !empty($Detalleordencompra[$i]['advaloremvalor']) ? ($Detalleordencompra[$i]['advaloremvalor']) : "0.00";
                    $tasadespacho = !empty($Detalleordencompra[$i]['costotasadesp']) ? ($Detalleordencompra[$i]['costotasadesp']) : "0.00";
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][advalorporcentaje]" value="' . $advaloremporcentaje . '" class="txtAdvaloremPDetalle numeric required" style="width:' . $tamano . '" ></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][advaloremvalor]" value="' . $advaloremvalor . '" class="txtAdvaloremVDetalle numeric required" style="width:' . $tamano . '"  ></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][costotasadesp]" value="' . $tasadespacho . '" class="txtTasaDespachoDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    //Gastos Variables:
                    $flat = !empty($Detalleordencompra[$i]['flat']) ? ($Detalleordencompra[$i]['flat']) : "0.00";
                    $VoBo = !empty($Detalleordencompra[$i]['VoBo']) ? ($Detalleordencompra[$i]['VoBo']) : "0.00";
                    $gate_in = !empty($Detalleordencompra[$i]['gate_in']) ? ($Detalleordencompra[$i]['gate_in']) : "0.00";
                    //$box_fee=!empty($Detalleordencompra[$i]['box_fee'])?($Detalleordencompra[$i]['box_fee']):"0.00";
                    //$insurance_fee=!empty($Detalleordencompra[$i]['insurance_fee'])?($Detalleordencompra[$i]['insurance_fee']):"0.00";
                    //$sobre_estadia=!empty($Detalleordencompra[$i]['sobre_estadia'])?($Detalleordencompra[$i]['sobre_estadia']):"0.00";
                    //$doc_fee=!empty($Detalleordencompra[$i]['doc_fee'])?($Detalleordencompra[$i]['doc_fee']):"0.00";
                    $agenteaduanas = !empty($Detalleordencompra[$i]['agenteaduanas']) ? ($Detalleordencompra[$i]['agenteaduanas']) : "0.00";
                    $gas_adm = !empty($Detalleordencompra[$i]['gas_adm']) ? ($Detalleordencompra[$i]['gas_adm']) : "0.00";
                    $cadic1 = !empty($Detalleordencompra[$i]['cv1']) ? ($Detalleordencompra[$i]['cv1']) : "0.00";
                    $cadic2 = !empty($Detalleordencompra[$i]['cv2']) ? ($Detalleordencompra[$i]['cv2']) : "0.00";
                    $cadic3 = !empty($Detalleordencompra[$i]['cv3']) ? ($Detalleordencompra[$i]['cv3']) : "0.00";
                    $fleteInterno = !empty($Detalleordencompra[$i]['fleteInterno']) ? ($Detalleordencompra[$i]['fleteInterno']) : "0.00";
                    $advalorenvalor = !empty($Detalleordencompra[$i]['advaloremvalor']) ? ($Detalleordencompra[$i]['advaloremvalor']) : "0.00";
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][flat]" value="' . $flat . '" class="txtFlatDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][VoBo]" value="' . $VoBo . '" class="txtVBDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][gate_in]" value="' . $gate_in . '" class="txtGateInDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    //echo '<td class=""><input type="text" name="Detalleordencompra['.($i+1).'][box_fee]" value="'.$box_fee.'" class="txtBoxFeeDetalle numeric required" style="width:'.$tamano.'" ></td>';
                    //echo '<td class=""><input type="text" name="Detalleordencompra['.($i+1).'][insurance_fee]" value="'.$insurance_fee.'" class="txtInsuranceFeeDetalle numeric required" style="width:'.$tamano.'" ></td>';
                    //echo '<td class=""><input type="text" name="Detalleordencompra['.($i+1).'][sobre_estadia]" value="'.$sobre_estadia.'" class="txtSobreestadiaDetalle numeric required" style="width:'.$tamano.'" ></td>';
                    //echo '<td class=""><input type="text" name="Detalleordencompra['.($i+1).'][doc_fee]" value="'.$doc_fee.'" class="txtDocFeeDetalle numeric required" style="width:'.$tamano.'" ></td>';
                    //echo '<td class=""><input type="text" name="Detalleordencompra['.($i+1).'][gas_adm]" value="'.$gas_adm.'" class="txtGasAdmDetalle numeric required" style="width:'.$tamano.'" ></td>';
                    $tablaContenido .= '<td class=""><input type="text" readonly name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][cv1]" value="' . $cadic1 . '" class="txtCV1Detalle numeric required" style="width:' . $tamano . '" ></td>';
                    $tablaContenido .= '<td class=""><input type="text" readonly name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][cv2]" value="' . $cadic2 . '" class="txtCV2Detalle numeric required" style="width:' . $tamano . '" ></td>';
                    $tablaContenido .= '<td class=""><input type="text" readonly name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][cv3]" value="' . $cadic3 . '" class="txtCV3Detalle numeric required" style="width:' . $tamano . '" ></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][agenteaduanas]" value="' . $agenteaduanas . '" class="txtAgenteAduanaDetalle numeric required" readonly style="width:' . $tamano . '" ></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][fleteInterno]" value="' . $fleteInterno . '" class="txtFleteInternoDetalle numeric required" readonly style="width:' . $tamano . '" ></td>';
                    //Calculos Finales
                    //$total=$ciftotal+$advalorenvalor+$tasadespacho+$flat+$VoBo+$gate_in+$box_fee+$insurance_fee+$sobre_estadia+$doc_fee+$gas_adm+$fleteInterno+$agenteaduanas+$cadic1+$cadic2+$cadic3;
                    $total = $ciftotal + $advalorenvalor + $tasadespacho + $fleteInterno + $flat + $VoBo + $gate_in + $agenteaduanas + $cadic1 + $cadic2 + $cadic3;
                    //$total=$total;
                    $totalunitario = $total / $cantidad;
                    $porcentaje = (($totalunitario - $fob) / $fob) * 100;
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][total]" value="' . $total . '" class="txtTotalDetalle numeric required" style="width:' . $tamanoG . '" readonly></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][totalunitario]" value="' . number_format($totalunitario, 2, '.', '') . '" class="txtTotalUnitarioDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][porcentaje]" value="' . number_format($porcentaje, 2, '.', '') . '" class="txtPorcentajeDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    if ($Ordencompra[0]['actualizado'] == 0) {
                        $tablaContenido .= '<td><a href="#"><img src="/imagenes/eliminar.gif" width="18px" class="eliminarProducto"></a></td>';
                    }
                    $tablaContenido .= '</tr>';
                }
            }
        }
        $rspta['cantidad'] = $cantidadActual + $tamDoc + 1;
        $rspta['contenido'] = $tablaContenido;
        header("Content-type: application/json");
        echo json_encode($rspta);
    }
    
    function listardetalleXocNuevo() {
        $idordencompra = $_REQUEST['idordencompra'];
        $cantidadActual = $_REQUEST['cantidad'];
        $tablaContenido = "";
        if (!empty($idordencompra) && $idordencompra > 0) {
            $ordenCompra = new Ordencompra();
            $Ordencompra = $ordenCompra->editaOrdenCompra($idordencompra);
            if ($Ordencompra[0]['valorizado'] == 1) {
                $Detalleordencompra = array();
                if ($Ordencompra[0]['nuevoformato']==1 &&$Ordencompra[0]['actualizado'] == 0) {
                    $docmodel = new Detalleordencompra();
                    $Detalleordencompra = $docmodel->listaDetalleOrdenCompra($idordencompra);
                }
                $empresa = $this->AutoLoadModel('empresa');
                $rutaImagen = $this->rutaImagenesProducto();

                $tamDoc = count($Detalleordencompra);
                $importeTotalOC = 0;
                for ($i = 0; $i < $tamDoc; $i++) {
                    if (empty($Detalleordencompra[$i]['imagen'])) {
                        $rutaCompleta = '/public/imagenes/sinFoto.jpg';
                    } else {
                        $rutaCompleta = $RutaImagen . $Detalleordencompra[$i]['codigopa'] . '/' . $Detalleordencompra[$i]['imagen'];
                    }
                    $tamano = '30px';
                    $tamanoM = '55px';
                    $tamanoG = '70px';
                    $tamanoGG = "100px";
                    $tablaContenido .= '<tr class="ColDoc_' . $idordencompra . '">';
                    $tablaContenido .= '<td class="">' . ($i + 1 + $cantidadActual) . '</td>';
                    //Codigo,Cantidad,Volumen,Fob,Fob Total
                    $iddetalleOC = $Detalleordencompra[$i]['iddetalleordencompra'];
                    $idproductoOC = $Detalleordencompra[$i]['idproducto'];
                    $cantidad = $Detalleordencompra[$i]['cantidadrecibidaoc'];

                    $fob = $Detalleordencompra[$i]['fobdoc'];
                    $piezas = !empty($Detalleordencompra[$i]['piezas']) ? $Detalleordencompra[$i]['piezas'] : 0;
                    $carton = !empty($Detalleordencompra[$i]['carton']) ? $Detalleordencompra[$i]['carton'] : 0;
                    $fobTotal = $fob * $cantidad;
                    $tablaContenido .= '<td class="codigo" style="width:' . $tamanoGG . '"><a href="/producto/editar/' . $idproductoOC . '" target="_blank">' . $Detalleordencompra[$i]['codigopa'] . "</a></td>";
                    $tablaContenido .= '<input type="hidden" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][iddetalleordencompra]" value="' . $iddetalleOC . '">';
                    $tablaContenido .= '<input type="hidden" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][idproducto]" value="' . $idproductoOC . '">';
                    $tablaContenido .= '<td class="codigo" style="width:150px">' . $Detalleordencompra[$i]['nompro'] . '</td>';
                    $tablaContenido .= '<td class="codigo" style="width:' . $tamano . '">' . $Detalleordencompra[$i]['marca'] . '</td>';
                    $tablaContenido .= '<td class="codigo" style="width:' . $tamano . '">' . $Detalleordencompra[$i]['codempaque'] . '</td>';
                    $tablaContenido .= '<td class="center"><input readonly type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][cantidadrecibidaoc]" class="txtCantidadDetalle numeric required" style="width:' . $tamano . ';color:red;" value="' . $cantidad . '"  ></td>';
                    $tablaContenido .= '<td class="codigo" style="width:' . $tamano . '">' . $Detalleordencompra[$i]['unidadmedida'] . '</td>';
                    $tablaContenido .= '<input type="hidden" name="Producto[' . ($i + 1 + $cantidadActual) . '][preciocosto]"  value="' . $Detalleordencompra[$i]['preciocosto'] . '">';

                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][piezas]" value="' . $piezas . '" class="txtPiezas numeric required" style="width:' . $tamano . '"></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][carton]" value="' . $carton . '" class="txtCarton numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][fobdoc]" class="txtfobDetalle numeric required" style="width:' . $tamano . '" value="' . $fob . '"></td>';
                    $tablaContenido .= '<td><input type="text" value="' . round($fobTotal, 2) . '" class="txtfobTotalDetalle numeric required" style="width:' . $tamanoM . '" readonly ></td>';
                    //$TOTALfobTotal += $fobTotal;
                    //Flete,Seguro,Cif,Cif Unit.
                    $flete = !empty($Detalleordencompra[$i]['fleted']) ? ($Detalleordencompra[$i]['fleted']) : "0.00";
                    $seguro = !empty($Detalleordencompra[$i]['seguro']) ? ($Detalleordencompra[$i]['seguro']) : "0.00";
                    $ciftotal = $fobTotal + $seguro + $flete;
                    //$TOTALflete += $flete;
                    //$TOTALseguro += $seguro;
                    //$TOTALciftotal += $ciftotal;

                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][fleted]" value="' . $flete . '" class="txtFleteDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][seguro]" value="' . $seguro . '" class="txtSeguroDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][ciftotal]" value="' . round($ciftotal, 2) . '" class="txtciftotal required" style="width:' . $tamanoM . '" readonly ></td>';
                    //$TOTALsada += $Detalleordencompra[$i]['sada'];
                    //$TOTALscto += $Detalleordencompra[$i]['scto'];
                    //$TOTALfargo += $Detalleordencompra[$i]['fargo'];
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][sada]" value="' . $Detalleordencompra[$i]['sada'] . '" class="txtSada numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td style="display: none;"><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][scto]" value="' . $Detalleordencompra[$i]['scto'] . '" class="txtSscto numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][fargo]" value="' . $Detalleordencompra[$i]['fargo'] . '" class="txtFargo numeric required" style="width:' . $tamano . '" readonly></td>';

                    $VoBo = !empty($Detalleordencompra[$i]['VoBo']) ? ($Detalleordencompra[$i]['VoBo']) : "0.00";
                    $fleteInterno = !empty($Detalleordencompra[$i]['fleteInterno']) ? ($Detalleordencompra[$i]['fleteInterno']) : "0.00";
                    $agenteaduanas = !empty($Detalleordencompra[$i]['agenteaduanas']) ? ($Detalleordencompra[$i]['agenteaduanas']) : "0.00";

                    //$TOTALVoBo += $VoBo;
                    //$TOTALdevctndr += $Detalleordencompra[$i]['devctndr'];
                    //$TOTALfleteInterno += $fleteInterno;
                    //$TOTALagenteaduanas += $agenteaduanas;
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][VoBo]" value="' . $VoBo . '" class="txtVBDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][devctndr]" value="' . $Detalleordencompra[$i]['devctndr'] . '" class="txtDevctndr numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][fleteInterno]" value="' . $fleteInterno . '" class="txtFleteInternoDetalle numeric required" readonly style="width:' . $tamano . '" ></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][agenteaduanas]" value="' . $agenteaduanas . '" class="txtAgenteAduanaDetalle numeric required" readonly style="width:' . $tamano . '" ></td>';

                    $advaloremporcentaje = !empty($Detalleordencompra[$i]['advalorporcentaje']) ? ($Detalleordencompra[$i]['advalorporcentaje']) : "0";
                    $advalorenvalor = !empty($Detalleordencompra[$i]['advaloremvalor']) ? ($Detalleordencompra[$i]['advaloremvalor']) : "0.00";                        

                    //$TOTALadvaloremvalor += $advalorenvalor;
                    //$TOTALgoemp += $Detalleordencompra[$i]['goemp'];                       
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][advalorporcentaje]" value="' . $advaloremporcentaje . '" class="txtadvalorporcentaje numeric required" style="width:' . $tamano . '"></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][advaloremvalor]" value="' . $advalorenvalor . '" class="txtadvaloremvalor numeric required" style="width:' . $tamano . '" readonly></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][goemp]" value="' . $Detalleordencompra[$i]['goemp'] . '" class="txtGoemp numeric required" style="width:' . $tamano . '" readonly></td>';

                    //Calculos Finales
                    //$total=$ciftotal+$advalorenvalor+$tasadespacho+$flat+$VoBo+$gate_in+$box_fee+$insurance_fee+$sobre_estadia+$doc_fee+$gas_adm+$fleteInterno+$agenteaduanas+$cadic1+$cadic2+$cadic3;
                    $total = $ciftotal + $Detalleordencompra[$i]['sada'] + $Detalleordencompra[$i]['scto'] + $Detalleordencompra[$i]['fargo'] + $VoBo + $Detalleordencompra[$i]['devctndr'] + $fleteInterno + $agenteaduanas + $advalorenvalor + $Detalleordencompra[$i]['goemp'];
                    //$total=$total;
                    $totalunitario = $fob*1.3;
                    $porcentaje = round((($total/$ciftotal) - 1), 2)*100 ;

                    //$TOTALporcentaje += $porcentaje;
                    //$TOTALcostototal += $total;
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][total]" value="' . $total . '" class="txtTotalDetalle numeric required" style="width:' . $tamanoG . '" readonly></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][porcentaje]" value="' . round($porcentaje, 2) . '" class="txtPorcentajeDetalle numeric required" style="width:' . $tamano . '"></td>';
                    $tablaContenido .= '<td class=""><input type="text" name="Detalleordencompra[' . ($i + 1 + $cantidadActual) . '][totalunitario]" value="' . round($totalunitario, 2) . '" class="txtTotalUnitarioDetalle numeric required" style="width:' . $tamano . '" readonly></td>';
                    if ($Ordencompra[0]['actualizado'] == 0) {
                        $tablaContenido .= '<td><a href="#"><img src="/imagenes/eliminar.gif" width="18px" class="eliminarProducto"></a></td>';
                    }
                    $tablaContenido .= '</tr>';
                }
            }
        }
        $rspta['cantidad'] = $cantidadActual + $tamDoc + 1;
        $rspta['contenido'] = $tablaContenido;
        header("Content-type: application/json");
        echo json_encode($rspta);
    }

    function guardarestructuradecostos() {
        $idordenescompras = $_REQUEST['idordenescompras'];
        $dataOC['serieDua'] = $_REQUEST['serieDua'];
        $dataOC['tipocompra'] = $_REQUEST['lsttipocompra'];
        $dataOC['nroDua'] = $_REQUEST['nroDua'];
        $dataOC['fechaCompraOC'] = date('Y-m-d', strtotime($_REQUEST['fechaCompraDua']));
        $dataDetalleEDC = $_REQUEST['Detalleordencompra'];
        $totalDOC = count($dataDetalleEDC);
        if ($totalDOC > 0) {
            $dataEDC = $_REQUEST['OrdenCompra'];
            $dataEDC['tipocompra'] = $dataOC['tipocompra'];
            $dataEDC['fechadua'] = $dataOC['fechaCompraOC'];
            $dataEDC['serieDua'] = $_REQUEST['serieDua'];
            $dataEDC['nroDua'] = $_REQUEST['nroDua'];
            $estructuraCostos = New Estructuradecostos();
            $idEDC = $estructuraCostos->graba($dataEDC);
            $totalDOC = $totalDOC + 1;
            for ($i = 1; $i <= $totalDOC; $i++) {
                if (!empty($dataDetalleEDC[$i]['iddetalleordencompra'])) {
                    $dataDetalleEDC[$i]['idestructuradecostos'] = $idEDC;
                    $estructuraCostos->grabaDetalle($dataDetalleEDC[$i]);
                } else {
                    if ($i < $totalDOC) {
                        $totalDOC++;
                    }
                }
            }
            $ordenCompra = new Ordencompra();
            $dataOC['idestructuradecostos'] = $idEDC;
            $dataOC['actualizado'] = 1;
            for ($i = 0; $i < count($idordenescompras); $i++) {
                $ordenCompra->actualizaOrdenCompra($dataOC, $idordenescompras[$i]);
            }
        }
        $ruta['ruta'] = "/ordencompra/actualizado/" . $idordenescompras[0];
        $this->view->show('ruteador.phtml', $ruta);
    }

}

?>