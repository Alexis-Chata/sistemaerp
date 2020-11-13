<?php

Class OrdenVentaController extends ApplicationGeneral {

    function nuevo() {
        $ordenVenta = new OrdenVenta();
        $condicionventa = new CondicionVenta();
        $data['tipocondicionventa'] = $condicionventa->listadocondicionventa();
        $data['codigo'] = $ordenVenta->buscarcodigo();
        $this->view->show("/ordenventa/nuevo.phtml", $data);
    }

    function grabaxpress() {
        $ordenVenta = new OrdenVenta();
        $detalleOrdenVenta = new DetalleOrdenVenta();
        $cliente = new Actor();
        $producto = new producto();
        $movimiento = new Movimiento();
        $detalleMovimiento = new Detallemovimiento();
        $ordenCobro = new OrdenCobro();
        $cliente = $this->AutoLoadModel('cliente');
        $clienteVendedor = $this->AutoLoadModel('clientevendedor');
        $ordenVentaDuracion = $this->AutoLoadModel('ordenventaduracion');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $dataOrdenVenta = $_REQUEST['OrdenVenta'];
        $dataOrdenVenta['tipo_letra'] = $_REQUEST['tipoLetra'];
        $dataOrdenVenta['importeaprobado'] = $dataOrdenVenta['importeov'];
        $dataOrdenVenta['codigov'] = "";
        $dataOrdenVenta['vbventas'] = 1;
        $dataOrdenVenta['mventas'] = "VENTA XPRESS - SUCURSAL PARURO";
        $dataOrdenVenta['vbcobranzas'] = 1;
        $dataOrdenVenta['mcobranzas'] = "VENTAS XPRESS - COBRANZAS";
        $dataOrdenVenta['vbalmacen'] = 1;
        $dataOrdenVenta['malmacen'] = "VENTAS XPRESS - ALMACEN";
        $dataOrdenVenta['vbcreditos'] = 1;
        $dataOrdenVenta['mcreditos'] = "VENTAS XPRESS - CREDITOS";
        $dataOrdenVenta['observaciones'] = "VENTAS XPRESS - SUCURSAL PARURO";
        $dataOrdenVenta['faprobado'] = date('Y-m-d');
        $dataOrdenVenta['es_contado'] = 1;
        $dataOrdenVenta['es_credito'] = 0;
        $dataOrdenVenta['es_letras'] = 0;
        $dataOrdenVenta['esdespachado'] = 1;
        $dataOrdenVenta['situacion'] = "cancelado";
        $dataDetalleOrdenVenta = $_REQUEST['DetalleOrdenVenta'];
        $exito1 = $ordenVenta->grabar($dataOrdenVenta);
        if ($exito1) {
            $codigov = strtoupper($ordenVenta->generaCodigo());
            $dataOrdenCodigoV['codigov'] = $codigov;
            $exitoA = $ordenVenta->actualizaOrdenVenta($dataOrdenCodigoV, $exito1);
            $dataCliente['idultimaorden'] = $exito1;
            $exitoc = $cliente->actualizaCliente($dataCliente, "idcliente='" . $dataOrdenVenta['idcliente'] . "'");
            $dataCV = $clienteVendedor->buscarxid($dataOrdenVenta['idcliente']);
            if (!empty($dataCV)) {
                $dataClienteVendedor['idvendedor'] = $dataOrdenVenta['idvendedor'];
                $exitocv = $clienteVendedor->actualizaClienteVendedor($dataOrdenVenta['idcliente'], $dataClienteVendedor);
            }
            $dm['idordenventa'] = $exito1;
            $dm['conceptomovimiento'] = 1;
            $dm['tipomovimiento'] = 2;
            $dm['idtipooperacion'] = 1;
            $dm['essunat'] = 1;
            $dm['fechamovimiento'] = date('Y/m/d');
            $exito2 = $movimiento->grabaMovimiento($dm);
            foreach ($dataDetalleOrdenVenta as $data) {
                $data['idordenventa'] = $exito1;
                $data['cantaprobada'] = $data['cantsolicitada'];
                $data['cantdespacho'] = $data['cantsolicitada'];
                $data['precioaprobado'] = $data['preciolista'];
                $data['preciofinal'] = $data['preciolista'];
                $exito3 = $detalleOrdenVenta->graba($data);
                //descontamos el stock disponible
                $idProducto = $data['idproducto'];
                $dataBusqueda = $producto->buscaProducto($idProducto);
                $stockdisponibleA = $dataBusqueda[0]['stockdisponible'];
                $data2['stockdisponible'] = ($stockdisponibleA - $data['cantsolicitada']);
                $data2['stockactual'] = ($stockdisponibleA - $data['cantsolicitada']);
                if ($data2['stockactual'] <= 0) {
                    $data2['esagotado'] = 1;
                    $data2['fechaagotado'] = date('Y-m-d');
                }
                $ddm['idmovimiento'] = $exito2;
                $ddm['idproducto'] = $idProducto;
                $ddm['pu'] = $data['preciolista'];
                $ddm['preciovalorizado'] = $pv[0]['preciolista'];
                $ddm['cantidad'] = $data['cantsolicitada'];
                $ddm['importe'] = $data['cantsolicitada'] * $data['total'];
                $ddm['stockdisponibledm'] = $data2['stockactual'];
                $ddm['stockactual'] = $data2['stockactual'];
                $exitoP = $producto->actualizaProducto($data2, $idProducto);
                $exito5M = $detalleMovimiento->grabaDetalleMovimieto($ddm);
            }
            $redondeo = $this->configIni('Globals', 'Redondeo');
            $dataOrden['escontado'] = 1;
            $dataOrden['importeordencobro'] = $dataOrdenVenta['importeov'];
            $dataOrden['saldoordencobro'] = 0;
            $dataOrden['idordenventa'] = $data['idordenventa'];
            $dataOrden['femision'] = date('Y/m/d');
            $dataOrden['numletras'] = 0;
            $exito3 = $ordenCobro->grabaOrdencobro($dataOrden);
            $dataGasto['idordenventa'] = $data['idordenventa'];
            $dataGasto['importegasto'] = round($dataOrden['importeordencobro'] - $dataOrden['importeordencobro'] / 1.18, $redondeo);
            $dataGasto['idtipogasto'] = 7;
            $grabaGasto = $ordenGasto->graba($dataGasto);
            $dataGasto['idtipogasto'] = 9;
            $dataGasto['importegasto'] = round($dataOrden['importeordencobro'] / 1.18, $redondeo);
            $grabaGasto = $ordenGasto->graba($dataGasto);
            if ($exito1 && $exito2 && $exito3) {/*
              foreach($dataDetalleOrdenVenta as $data){
              $stockNuevo=$data['stockactual']-$data['cantidad'];
              $ddm['idmovimiento']=$exito2;
              $idproducto=$ddm['idproducto']=$data['idproducto'];
              $ddm['pu']=$data['pu'];
              $pv=$producto->buscaProducto($idproducto);
              $ddm['preciovalorizado']=$pv[0]['preciocosto'];
              $ddm['cantidad']=$data['cantidad'];
              $ddm['importe']=$data['total'];
              $ddm['stockdisponibledm']=$stockNuevo;
              $ddm['stockactual']=$data['stockactual']-$data['cantidad'];
              $dataPro['stockactual']=$stockNuevo;
              if ($stockNuevo<=0) {
              $dataPro['esagotado']=1;
              $dataPro['fechaagotado']=date('Y-m-d');
              }
              $exito4=$producto->actualizaProducto($dataPro,$data['idproducto']);
              $exito5=$detalleMovimiento->grabaDetalleMovimieto($ddm);
              } */
                //if($exito4 && $exito5){					
                $clienteposicion = New Cliente();
                $idcliente = $clienteposicion->idclientexidordenventa($dataOrden['idordenventa']);
                $exito_cp = $clienteposicion->restarSaldo($idcliente, $dataOrden['importeordencobro']);

                $fechaActual = date('Y/m/d');
                $ddoc['idordencobro'] = $exito3;
                $ddoc['fvencimiento'] = $fechaActual;
                $mContado = $dataOrden['importeordencobro'];
                $exito6 = $this->grabaContadoPagado($mContado, $exito3, $fechaActual, $dataOrden['idordenventa']);
                //}
            }
            $dataDuracion['idordenventa'] = $exito1;
            $dataDuracion['tiempo'] = strtotime(0);
            $dataDuracion['referencia'] = 'credito';
            $exito4 = $ordenVentaDuracion->grabaOrdenVentaDuracion($dataDuracion);
            if ($exito3) {
                $ingreso['idcliente'] = $dataOrdenVenta['idcliente'];
                $ingreso['idOrdenVenta'] = $exito1;
                $ingreso['esvalidado'] = 1;
                $ingreso['fcobro'] = date('Y/m/d');
                $ingreso['idcobrador'] = $dataOrdenVenta['iddespachador'];
                $ingreso['tipocobro'] = 1;
                $ingreso['montoingresado'] = $mContado;
                $ingreso['montoasignado'] = $mContado;
                $ingreso['finicio'] = date('Y/m/d');
                $ingreso['fvencimiento'] = date('Y/m/d');
                $ingreso['observaciones'] = "INGRESO - SUSCURSAL PARURO";
                $ingreso['saldo'] = 0;
                $objIngreso = New Ingresos();
                $graba = $objIngreso->graba($ingreso);
                if ($_SESSION['idrol'] == 25) {
                    $ruta['ruta'] = "/vendedor/misordenes";
                    $this->view->show("ruteador.phtml", $ruta);
                } else {
                    $ruta['ruta'] = "/ventas/creaguiaped/" . $codigov;
                    $this->view->show("ruteador.phtml", $ruta);
                }
            }
        }
    }

    function grabaContadoPagado($monto = '', $idoc, $fechaActual, $id) {
        $detalleOrdenCobro = new detalleOrdenCobro();
        $ordenVenta = $this->AutoLoadModel('ordenventa');
        if ($monto != '') {
            $data['idordencobro'] = $idoc;
            $data['fechagiro'] = $fechaActual;
            $data['fvencimiento'] = date('Y/m/d', strtotime($fechaActual));
            $data['importedoc'] = $monto;
            $data['saldodoc'] = 0;
            $data['formacobro'] = 1;
            $data['situacion'] = 'cancelado';
            $od['fechavencimiento'] = $data['fvencimiento'];
            $grabando = $ordenVenta->actualizaOrdenVenta($od, $id);
            return $detalleOrdenCobro->grabaDetalleOrdenVentaCobro($data);
        }
    }

    function graba() {
        $ordenVenta = new OrdenVenta();
        $detalleOrdenVenta = new DetalleOrdenVenta();
        $cliente = new Actor();
        $producto = new producto();
        $cliente = $this->AutoLoadModel('cliente');
        $clienteVendedor = $this->AutoLoadModel('clientevendedor');
        $ordenVentaDuracion = $this->AutoLoadModel('ordenventaduracion');
        $dataOrdenVenta = $_REQUEST['OrdenVenta'];
        $dataOrdenVenta['tipo_letra'] = $_REQUEST['tipoLetra'];
        $dataOrdenVenta['importeaprobado'] = $dataOrdenVenta['importeov'];
        $dataOrdenVenta['codigov'] = "";
        $dataDetalleOrdenVenta = $_REQUEST['DetalleOrdenVenta'];
        $exito1 = $ordenVenta->grabar($dataOrdenVenta);
        if ($exito1) {
            $codigov = strtoupper($ordenVenta->generaCodigo());
            $dataOrden['codigov'] = $codigov;
            $exitoA = $ordenVenta->actualizaOrdenVenta($dataOrden, $exito1);
            $dataCliente['idultimaorden'] = $exito1;
            $exitoc = $cliente->actualizaCliente($dataCliente, "idcliente='" . $dataOrdenVenta['idcliente'] . "'");
            $dataCV = $clienteVendedor->buscarxid($dataOrdenVenta['idcliente']);
            if (!empty($dataCV)) {
                $dataClienteVendedor['idvendedor'] = $dataOrdenVenta['idvendedor'];
                $exitocv = $clienteVendedor->actualizaClienteVendedor($dataOrdenVenta['idcliente'], $dataClienteVendedor);
            }
            foreach ($dataDetalleOrdenVenta as $data) {
                $data['idordenventa'] = $exito1;
                $exito3 = $detalleOrdenVenta->graba($data);
                //descontamos el stock disponible
                $idProducto = $data['idproducto'];
                $dataBusqueda = $producto->buscaProducto($idProducto);
                $stockdisponibleA = $dataBusqueda[0]['stockdisponible'];
                $data2['stockdisponible'] = ($stockdisponibleA - $data['cantsolicitada']);
                $exitoP = $producto->actualizaProducto($data2, $idProducto);
            }
            $dataDuracion['idordenventa'] = $exito1;
            $dataDuracion['tiempo'] = strtotime(0);
            $dataDuracion['referencia'] = 'creacion';
            $exito4 = $ordenVentaDuracion->grabaOrdenVentaDuracion($dataDuracion);
            if ($exito3) {
                /**
                 * Se modifica esta seccion a pedido del cliente por cuestiones de negocio.
                 */
                //$ruta['ruta']="/ventas/creaguiaped";
                if ($_SESSION['idrol'] == 25) {
                    $ruta['ruta'] = "/vendedor/misordenes";
                    $this->view->show("ruteador.phtml", $ruta);
                } else {
                    $ruta['ruta'] = "/ventas/creaguiaped/" . $codigov;
                    $this->view->show("ruteador.phtml", $ruta);
                }
            }
        }
    }

    function buscarautocompletecompletopendientes() {
        $tex = $_REQUEST['term'];
        $cliente = new OrdenVenta();
        $data = $cliente->buscaOrdenVentaCompletoPendiente($tex);
        echo json_encode($data);
    }

    function buscarautocomplete() {
        $tex = $_REQUEST['term'];
        $cliente = new OrdenVenta();
        $data = $cliente->buscaclienteautocomplete($tex);
        echo json_encode($data);
    }

    function buscarautocompletedni() {
        $tex = $_REQUEST['term'];
        $cliente = new OrdenVenta();
        $data = $cliente->buscaclienteautocompletedni($tex);
        echo json_encode($data);
    }

    function buscarautocompletecompleto() {
        $tex = $_REQUEST['term'];
        $cliente = new OrdenVenta();
        $data = $cliente->buscaOrdenVentaCompleto($tex);
        echo json_encode($data);
    }

    function buscarautocompleteagencia() {
        $tex = $_REQUEST['term'];
        $agencia = new OrdenVenta();
        $data = $agencia->buscaagenciaautocomplete($tex);
        echo json_encode($data);
    }

    function detalle() {
        $id = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $id = $_REQUEST['IDOV'];
        }
        $dataGuia = $this->AutoLoadModel("OrdenVenta");
        $idTipoCambio = $dataGuia->BuscarCampoOVxId($id, "IdTipoCambioVigente"); //PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA
        $TipoCambio = $this->AutoLoadModel("TipoCambio");
        $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
        $simboloMoneda = $dataTipoCambio[0]['simbolo'];
        $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
        $detalle = new detalleOrdenVenta();
        $almacen = new almacen();
        $datos = $detalle->listaDetalleOrdenVenta($id);
        $numero = 1;
        $totalGeneral = 0;
        foreach ($datos as $data) {
            $dataAlmacen = $almacen->buscaAlmacen($data['idalmacen']);
            $total = ($data['cantdespacho'] * $data['preciofinal']);
            echo '<tr>';
            echo    '<td class="center">' . ($numero) . '</td>';
            echo    '<td>' . $data['codigopa'] . '</td>';
            echo    '<td>' . $data['nompro'] . '</td>';
            echo    '<td>' . $dataAlmacen[0]['codigoalmacen'] . '</td>';
            echo    '<td class="center">' . $data['cantsolicitada'] . '</td>';
            echo    '<td class="center bold">' . $data['cantaprobada'] . '</td>';
            echo    '<td class="center bold">' . $data['cantdespacho'] . '</td>';
            echo    '<td class="center">' . $simboloMoneda . ' ' . number_format($data['preciosolicitado'], 2) . '</td>';
            echo    '<td class="center bold">' . $simboloMoneda . ' ' . number_format($data['precioaprobado'], 2) . '</td>';
            echo    '<td class="center">' . $simboloMoneda . ' ' . number_format($data['preciofinal'], 2) . '</td>';
            echo    '<td class="right">' . $simboloMoneda . ' ' . number_format($total, 2) . '</td>';
            echo "</tr>";
            $numero += 1;
            $totalGeneral += $total;
        }
        echo '<tr class="bold"><td colspan="10" class="right">TOTAL:</td><td class="right">' . $simboloMoneda . ' ' . number_format($totalGeneral, 2) . '</td></tr>';
        echo "<script>" .
        "$('#txtObservacionVentas').val('" . $datos[0]['mventas'] . "');" .
        "$('#txtObservacionCobranzas').val('" . $datos[0]['mcobranzas'] . "');" .
        "$('#txtObservacionCreditos').val('" . $datos[0]['mcreditos'] . "');" .
        "</script>";
    }

    function buscar() {
        $id = $_REQUEST['id'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscarEmisionLetra($id);
        echo "<tr>";
        echo    '<td>' . $data[0]['idorden'] . '</td>';
        echo    '<td>' . date("d-m-Y", strtotime($data[0]['fecha'])) . '</td>';
        echo    '<td>' . $data[0]['nombres'] . " " . $data[0]['apellidopaterno'] . " " . $data[0]['apellidomaterno'] . '</td>';
        echo    '<td>' . number_format($data[0]['importe'], 2) . '</td>';
        echo "<tr>";
    }
    
    function buscarfactura2() {
        $id = $_REQUEST['id'];
        $ordenVenta = new OrdenVenta();
        $clienteLugar = new Cliente();
        $transpote = new Transporte();
        $data = $ordenVenta->buscarxid($id);
        $dataTransporte = $transpote->buscarxIdClienteTransporte($data[0]['idclientetransporte']);
        $dataCliente = $clienteLugar->buscaClienteLugar($data[0]['idcliente']);
        $dataRespuesta['idordenventa'] = $id;
        $dataRespuesta['cliente'] = empty($data[0]['razonsocial']) ? '' : (html_entity_decode($data[0]['razonsocial'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['codigov'] = empty($data[0]['codigov']) ? '' : $data[0]['codigov'];
        $dataRespuesta['idcliente'] = empty($data[0]['idcliente']) ? '' : $data[0]['idcliente'];
        $dataRespuesta['cdireccion'] = empty($data[0]['direccion']) ? '' : (html_entity_decode($data[0]['direccion'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['ctelefono'] = empty($data[0]['telefono']) ? '' : $data[0]['telefono'];
        $dataRespuesta['direccionpartida'] = empty($data[0]['diralm']) ? '' : (html_entity_decode($data[0]['diralm'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['direccionllegada'] = empty($data[0]['direccion_despacho']) ? '' : (html_entity_decode($data[0]['direccion_despacho'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['direccionfiscal'] = empty($data[0]['direccion_envio']) ? '' : (html_entity_decode($data[0]['direccion_envio'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['contacto'] = empty($data[0]['contacto']) ? '' : (html_entity_decode($data[0]['contacto'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['fechaguia'] = empty($data[0]['fordenventa']) ? '' : (date("d-m-Y", strtotime($data[0]['fordenventa'])));
        $dataRespuesta['formaPago'] = empty($data[0]['formapagoov']) ? '' : $data[0]['formapagoov'];
        $dataRespuesta['ruc'] = $data[0]['ruc'];
        $dataRespuesta['dni'] = $data[0]['dni'];
        $dataRespuesta['observaciones'] = empty($data[0]['observaciones']) ? '' : (html_entity_decode($data[0]['observaciones'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['mventas'] = empty($data[0]['mventas']) ? '' : (html_entity_decode($data[0]['mventas'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['malmacen'] = empty($data[0]['malmacen']) ? '' : (html_entity_decode($data[0]['malmacen'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['mcobranzas'] = empty($data[0]['mcobranzas']) ? '' : (html_entity_decode($data[0]['mcobranzas'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['lugar'] = empty($dataCliente[0]['nombredistrito']) ? '' : ($dataCliente[0]['nombredistrito'] . " - " . $dataCliente[0]['nombreprovincia'] . " - " . $dataCliente[0]['nombredepartamento']);
        $dataRespuesta['trazonsocial'] = empty($dataTransporte[0]['trazonsocial']) ? '' : (html_entity_decode($dataTransporte[0]['trazonsocial'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['truc'] = empty($dataTransporte[0]['truc']) ? '' : $dataTransporte[0]['truc'];
        $dataRespuesta['tdireccion'] = empty($dataTransporte[0]['tdireccion']) ? '' : (html_entity_decode($dataTransporte[0]['tdireccion'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['tipoDocumento'] = empty($data[0]['tipodoccli']) ? '' : $data[0]['tipodoccli'];
        echo json_encode($dataRespuesta);
    }
    
    function buscarFactura() {
        $id = $_REQUEST['id'];
        $ordenVenta = new OrdenVenta();
        $clienteLugar = new Cliente();
        $transpote = new Transporte();
        $data = $ordenVenta->buscarxid($id);
        $dataTransporte = $transpote->buscarxIdClienteTransporte($data[0]['idclientetransporte']);
        $dataCliente = $clienteLugar->buscaClienteLugar($data[0]['idcliente']);
        $dataRespuesta['idordenventa'] = $id;
        $dataRespuesta['cliente'] = empty($data[0]['razonsocial']) ? '' : (html_entity_decode($data[0]['razonsocial'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['codigov'] = empty($data[0]['codigov']) ? '' : $data[0]['codigov'];
        $dataRespuesta['idcliente'] = empty($data[0]['idcliente']) ? '' : $data[0]['idcliente'];
        $dataRespuesta['cdireccion'] = empty($data[0]['direccion']) ? '' : (html_entity_decode($data[0]['direccion'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['ctelefono'] = empty($data[0]['telefono']) ? '' : $data[0]['telefono'];
        $dataRespuesta['direccionpartida'] = empty($data[0]['diralm']) ? '' : (html_entity_decode($data[0]['diralm'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['direccionllegada'] = empty($data[0]['direccion_despacho']) ? '' : (html_entity_decode($data[0]['direccion_despacho'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['direccionfiscal'] = empty($data[0]['direccion_envio']) ? '' : (html_entity_decode($data[0]['direccion_envio'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['contacto'] = empty($data[0]['contacto']) ? '' : (html_entity_decode($data[0]['contacto'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['fechaguia'] = empty($data[0]['fordenventa']) ? '' : (date("d-m-Y", strtotime($data[0]['fordenventa'])));
        $dataRespuesta['formaPago'] = empty($data[0]['formapagoov']) ? '' : $data[0]['formapagoov'];
        $dataRespuesta['rucdni'] = empty($data[0]['ruc']) ? $data[0]['dni'] : $data[0]['ruc'];
        $dataRespuesta['observaciones'] = empty($data[0]['observaciones']) ? '' : (html_entity_decode($data[0]['observaciones'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['mventas'] = empty($data[0]['mventas']) ? '' : (html_entity_decode($data[0]['mventas'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['malmacen'] = empty($data[0]['malmacen']) ? '' : (html_entity_decode($data[0]['malmacen'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['mcobranzas'] = empty($data[0]['mcobranzas']) ? '' : (html_entity_decode($data[0]['mcobranzas'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['lugar'] = empty($dataCliente[0]['nombredistrito']) ? '' : ($dataCliente[0]['nombredistrito'] . " - " . $dataCliente[0]['nombreprovincia'] . " - " . $dataCliente[0]['nombredepartamento']);
        $dataRespuesta['trazonsocial'] = empty($dataTransporte[0]['trazonsocial']) ? '' : (html_entity_decode($dataTransporte[0]['trazonsocial'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['truc'] = empty($dataTransporte[0]['truc']) ? '' : $dataTransporte[0]['truc'];
        $dataRespuesta['tdireccion'] = empty($dataTransporte[0]['tdireccion']) ? '' : (html_entity_decode($dataTransporte[0]['tdireccion'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['tipoDocumento'] = empty($data[0]['tipodoccli']) ? '' : $data[0]['tipodoccli'];
        echo json_encode($dataRespuesta);
    }

    function buscarguia() {
        $id = $_REQUEST['id'];
        $ordenVenta = new OrdenVenta();
        $clienteLugar = new Cliente();
        $transpote = new Transporte();
        $data = $ordenVenta->buscarxidguia($id);
        $dataTransporte = $transpote->buscarxIdClienteTransporte($data[0]['idclientetransporte']);
        $dataCliente = $clienteLugar->buscaClienteLugar($data[0]['idcliente']);
        $dataRespuesta['idordenventa'] = $id;
        $dataRespuesta['cliente'] = empty($data[0]['razonsocial']) ? '' : (html_entity_decode($data[0]['razonsocial'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['codigov'] = empty($data[0]['codigov']) ? '' : $data[0]['codigov'];
        $dataRespuesta['idcliente'] = empty($data[0]['idcliente']) ? '' : $data[0]['idcliente'];
        $dataRespuesta['cdireccion'] = empty($data[0]['direccion']) ? '' : (html_entity_decode($data[0]['direccion'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['ctelefono'] = empty($data[0]['telefono']) ? '' : $data[0]['telefono'];
        $dataRespuesta['direccionpartida'] = empty($data[0]['diralm']) ? '' : (html_entity_decode($data[0]['diralm'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['direccionllegada'] = empty($data[0]['direccion_despacho']) ? '' : (html_entity_decode($data[0]['direccion_despacho'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['direccionfiscal'] = empty($data[0]['direccion_envio']) ? '' : (html_entity_decode($data[0]['direccion_envio'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['contacto'] = empty($data[0]['contacto']) ? '' : (html_entity_decode($data[0]['contacto'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['fechaguia'] = empty($data[0]['fordenventa']) ? '' : (date("d-m-Y", strtotime($data[0]['fordenventa'])));
        $dataRespuesta['formaPago'] = empty($data[0]['formapagoov']) ? '' : $data[0]['formapagoov'];
        $dataRespuesta['rucdni'] = empty($data[0]['ruc']) ? '' : $data[0]['ruc'];
        $dataRespuesta['observaciones'] = empty($data[0]['observaciones']) ? '' : (html_entity_decode($data[0]['observaciones'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['mventas'] = empty($data[0]['mventas']) ? '' : (html_entity_decode($data[0]['mventas'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['malmacen'] = empty($data[0]['malmacen']) ? '' : (html_entity_decode($data[0]['malmacen'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['mcobranzas'] = empty($data[0]['mcobranzas']) ? '' : (html_entity_decode($data[0]['mcobranzas'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['lugar'] = empty($dataCliente[0]['nombredistrito']) ? '' : ($dataCliente[0]['nombredistrito'] . " - " . $dataCliente[0]['nombreprovincia'] . " - " . $dataCliente[0]['nombredepartamento']);
        $dataRespuesta['trazonsocial'] = empty($dataTransporte[0]['trazonsocial']) ? '' : (html_entity_decode($dataTransporte[0]['trazonsocial'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['truc'] = empty($dataTransporte[0]['truc']) ? '' : $dataTransporte[0]['truc'];
        $dataRespuesta['tdireccion'] = empty($dataTransporte[0]['tdireccion']) ? '' : (html_entity_decode($dataTransporte[0]['tdireccion'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['tipoDocumento'] = empty($data[0]['tipodoccli']) ? '' : $data[0]['tipodoccli'];
        echo json_encode($dataRespuesta);
    }

    function buscarFacturados() {
        $id = $_REQUEST['id'];
        $ordenVenta = new OrdenVenta();
        $clienteLugar = new Cliente();
        $transpote = new Transporte();
        $data = $ordenVenta->buscarxidFacturado($id);
        $dataTransporte = $transpote->buscarxIdClienteTransporte($data[0]['idclientetransporte']);
        $dataCliente = $clienteLugar->buscaClienteLugar($data[0]['idcliente']);
        $dataRespuesta['idordenventa'] = $id;
        $dataRespuesta['cliente'] = empty($data[0]['razonsocial']) ? '' : (html_entity_decode($data[0]['razonsocial'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['importeov'] = empty($data[0]['importeov']) ? 0 : $data[0]['importeov'];
        $dataRespuesta['codigov'] = empty($data[0]['codigov']) ? '' : $data[0]['codigov'];
        $dataRespuesta['idcliente'] = empty($data[0]['idcliente']) ? '' : $data[0]['idcliente'];
        $dataRespuesta['cdireccion'] = empty($data[0]['direccion']) ? '' : (html_entity_decode($data[0]['direccion'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['ctelefono'] = empty($data[0]['telefono']) ? '' : $data[0]['telefono'];
        $dataRespuesta['direccionpartida'] = empty($data[0]['diralm']) ? '' : (html_entity_decode($data[0]['diralm'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['direccionllegada'] = empty($data[0]['direccion_despacho']) ? '' : (html_entity_decode($data[0]['direccion_despacho'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['direccionfiscal'] = empty($data[0]['direccion_envio']) ? '' : (html_entity_decode($data[0]['direccion_envio'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['contacto'] = empty($data[0]['contacto']) ? '' : (html_entity_decode($data[0]['contacto'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['fechaguia'] = empty($data[0]['fordenventa']) ? '' : (date("d-m-Y", strtotime($data[0]['fordenventa'])));
        $dataRespuesta['formaPago'] = empty($data[0]['formapagoov']) ? '' : $data[0]['formapagoov'];
        $dataRespuesta['rucdni'] = empty($data[0]['ruc']) ? '' : $data[0]['ruc'];
        $dataRespuesta['observaciones'] = empty($data[0]['observaciones']) ? '' : (html_entity_decode($data[0]['observaciones'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['mventas'] = empty($data[0]['mventas']) ? '' : (html_entity_decode($data[0]['mventas'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['mcobranzas'] = empty($data[0]['mcobranzas']) ? '' : (html_entity_decode($data[0]['mcobranzas'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['lugar'] = empty($dataCliente[0]['nombredistrito']) ? '' : ($dataCliente[0]['nombredistrito'] . " - " . $dataCliente[0]['nombreprovincia'] . " - " . $dataCliente[0]['nombredepartamento']);
        $dataRespuesta['trazonsocial'] = empty($dataTransporte[0]['trazonsocial']) ? '' : (html_entity_decode($dataTransporte[0]['trazonsocial'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['truc'] = empty($dataTransporte[0]['truc']) ? '' : $dataTransporte[0]['truc'];
        $dataRespuesta['tdireccion'] = empty($dataTransporte[0]['tdireccion']) ? '' : (html_entity_decode($dataTransporte[0]['tdireccion'], ENT_QUOTES, 'UTF-8'));
        $dataRespuesta['tipoDocumento'] = empty($data[0]['tipodoccli']) ? '' : $data[0]['tipodoccli'];
        echo json_encode($dataRespuesta);
    }

    function autocompleteCancelados() {
        $tex = $_REQUEST['term'];
        $cliente = new OrdenVenta();
        $data = $cliente->autocompleteCancelados($tex);
        echo json_encode($data);
    }

    function autocompleteParaLetras() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->autocompleteParaLetras($texIni);
        echo json_encode($data);
    }

    function autocomplete() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $fact = $_REQUEST['id'];
        $data = $ordenVenta->buscaAutocomplete($texIni, $fact);
        echo json_encode($data);
    }

    function autocompletePercepcion() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaAutocompletePercepcion($texIni);
        echo json_encode($data);
    }

    function autocompleteguiaremision() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaAutocompleteGuiaRemision($texIni);
        echo json_encode($data);
    }

    function buscarCliente() {
        $idordenventa = $_REQUEST['id'];
        $cliente = New Cliente();
        $cliente = $cliente->buscarClienteOrdenVenta($idordenventa);
        $data['razonsocial'] = html_entity_decode($cliente[0]['razonsocial']);
        $data['ruc'] = $cliente[0]['ruc'];
        $data['direccion'] = $cliente[0]['direccion'];
        echo json_encode($data);
    }

    function listaDetalle() {
        $idGuia = $_REQUEST['id'];
        $dataGuia = $this->AutoLoadModel("OrdenVenta");
        $idTipoCambio = $dataGuia->BuscarCampoOVxId($idGuia, "IdTipoCambioVigente"); //PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA
        $TipoCambio = $this->AutoLoadModel("TipoCambio");
        $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
        $simboloMoneda = $dataTipoCambio[0]['simbolo'];
        $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
        $porcentaje = $_REQUEST['porcentaje'];
        $modo = $_REQUEST['modo'];
        $detalleOrdenVenta = new detalleOrdenVenta();
        $data = $detalleOrdenVenta->listaDetalleOrdenVenta($idGuia);
        $total = 0;
        $descuento = New Descuento();
        $dataDescuento = $descuento->listado();
        $cantidaddesc = count($dataDescuento);
        for ($i = 0; $i < $cantidaddesc; $i++) {
            $dscto[$dataDescuento[$i]['id']] = $dataDescuento[$i]['valor'];
        }
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['idmoneda'] == 2) {
                $data[$i]['preciolista'] = $data[$i]['preciolistadolares'];
            }
            echo "<tr>";
            if ($porcentaje != "") {
                if ($modo == 1) {
                    $precio = $data[$i]['preciolista'];
                    $data[$i]['preciolista'] = (($precio * $porcentaje) / 100);
                } else {
                    $cantidad = $data[$i]['cantsolicitada'];
                    $data[$i]['cantsolicitada'] = ceil(($cantidad * $porcentaje) / 100);
                }
            }

            for ($y = 0; $y < $cantidaddesc; $y++) {
                if ($dataDescuento[$y]['id'] == $data[$i]['descuentosolicitado']) {

                    $descuentoeligidoprev = $dataDescuento[$y]['dunico'];
                    break;
                } else {
                    $descuentoeligidoprev = 0;
                }
            }
            $preciosolicitado = ($data[$i]['descuentooferta'] == 0 ? $data[$i]['preciosolicitado'] : $data[$i]['precioofertado']);
            $precioTotal = (($preciosolicitado) * ($data[$i]['cantsolicitada']));
            
            echo '<td>' . $data[$i]['codigopa'] . '</td>';
            echo '<td>' . $data[$i]['nompro'] . '</td>';
            echo '<td style="text-align: right;">' . $simboloMoneda . ' ' . number_format($data[$i]['preciocosto'] / $TC_PrecioVenta, 2) . '</td>';
            echo '<td style="text-align: right;">' . $simboloMoneda . ' ' . number_format($data[$i]['precioreferencia01'] / $TC_PrecioVenta, 2) . '</td>';
            //echo '<td style="text-align: right;">' . $simboloMoneda . ' ' . number_format($data[$i]['preciolista'] / $TC_PrecioVenta, 2) . '</td>';
            echo '<td style="text-align: right;">' . $simboloMoneda . ' ' . number_format($data[$i]['preciooferta'] / $TC_PrecioVenta, 2, '.', '') . '</td>';
            echo '<td style="text-align: center;">' . $data[$i]['cantsolicitada'] . '</td>';
            echo '<td class="center">' .
                    '<input type="text" name="DetalleOrdenVenta[' . $i . '][cantaprobada]" value="' . $data[$i]['cantsolicitada'] . '" class="txtCantidadAprobada text-50 numeric" readonly>' .
                    '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][estado]" value="1" class="txtEstado">' .
                    '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][iddetalleordenventa]" value="' . $data[$i]['iddetalleordenventa'] . '">' .
                    '<input type="hidden" class="idproducto" value="' . $data[$i]['idproducto'] . '">' .
                    '<input type="hidden" class="cantidadInicial" value="' . $data[$i]['cantsolicitada'] . '">' .
                    '<input type="hidden" name="Producto[' . $i . '][idproducto]" value="' . $data[$i]['idproducto'] . '" >' .
                    '<input type="hidden" name="Producto[' . $i . '][cantsolicitada]" value="' . $data[$i]['cantsolicitada'] . '" >' .
                 '</td>';
            echo '<td class="right">' . $simboloMoneda . ' <input type="text" name="DetalleOrdenVenta[' . $i . '][precioaprobado]" value="' . number_format($data[$i]['preciolista2'], 2, '.', '') . '" class="txtPrecioLista2 text-50" readonly></td>';
            echo '<td><select name="DetalleOrdenVenta[' . $i . '][descuentoaprobado]" class="txtDescuentoAprobado">';
            echo '<option value="0"> Elija descuento</option>';
            for ($y = 0; $y < $cantidaddesc; $y++) {
                if ($dataDescuento[$y]['id'] == $data[$i]['descuentosolicitado']) {
                    echo '<option selected value="' . $dataDescuento[$y]['id'] . '"> ' . $dataDescuento[$y]['valor'] . '</option>';
                    $descuentoeligido = $dataDescuento[$y]['dunico'];
                    $descuentotextoeligido = $dataDescuento[$y]['valor'];
                } else {
                    echo '<option value="' . $dataDescuento[$y]['id'] . '"> ' . $dataDescuento[$y]['valor'] . '</option>';
                }
            }
            echo '</select>
                    <input type="hidden" value="' . $descuentoeligido . '"  class="valorunico" name="DetalleOrdenVenta[' . $i . '][descuentoaprobadovalor]">
                    <input type="hidden" value="' . $descuentotextoeligido . '"  class="textounico" name="DetalleOrdenVenta[' . $i . '][descuentoaprobadotexto]"></td>';
            echo '<td class="right">' . $simboloMoneda . ' <input type="text" name="DetalleOrdenVenta[' . $i . '][preciosolicitado]" value="' . number_format($data[$i]['preciosolicitado'], 2, '.', '') . '" class="txtPrecioAprobado text-50" readonly></td>';
            echo '<td style="text-align: center">' .
                        '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][descuentooferta]" value="' . $data[$i]['descuentooferta'] . '" class="textDescOferta text-50 numeric" readonly>' .
                        '<label class="txtDescOferta">' . ($data[$i]['descuentooferta'] == 0 ? ' - ' : round($data[$i]['descuentooferta'], 2) . "%") . '</label>' .
                 '</td>';
            
            echo '<td >' . $simboloMoneda . '  <input style="text-align: right;" type="text" name="DetalleOrdenVenta[' . $i . '][preciofinal]" value="' . round($preciosolicitado, 2) . '" class="txtPrecioFinal text-50"></td>';
            
            echo '<td class="right">' . $simboloMoneda . '  <input type="text" value="' . number_format($precioTotal, 2, '.', '') . '" class="txtTotal text-100 right" readonly></td>';
            
            echo '<td><a href="#" class="btnEditarItem"><img src="/imagenes/editar.gif"></a></td>';
            echo '<td><!--<a href="#" class="btnEliminarItem"><img src="/imagenes/eliminar.gif"></a>--></td>';
            echo "</tr>";
            $total += $precioTotal;
            $descuentoeligido = "";
            $descuentotextoeligido = "";
        }
        echo '<tr style="color:#f00">';
        echo '<td colspan="11" class="right bold">
                    <input type="text" disabled value="Precio de Venta ' . $simboloMoneda . '"><br>
                    <input type="text" disabled value="I.G.V. ' . $simboloMoneda . '"><br>
                    <input type="text" disabled value="Total a Pagar ' . $simboloMoneda . '"><br>					
            </td>';
        echo '<td class="right">' .
                '<input type="text" id="txtSubTotal" value="' . number_format($total - (($total * 19) / 100), 2, '.', '') . '" class="text-100 right" readonly><br>' .
                '<input type="text" id="txtIgv" value="' . number_format(($total * 19) / 100, 2, '.', '') . '" class="text-100 right" readonly><br>' .
                '<input type="text" id="txtTotal" value="' . number_format(($total), 2, '.', '') . '" class="text-100 right" readonly>' .
            '</td>';
        echo "</tr>";
    }

    function listaDetalleAlmacen() {
        $idGuia = $_REQUEST['id'];
        $dataGuia = $this->AutoLoadModel("OrdenVenta");
        $idTipoCambio = $dataGuia->BuscarCampoOVxId($idGuia, "IdTipoCambioVigente"); //PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA
        $TipoCambio = $this->AutoLoadModel("TipoCambio");
        $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
        $simboloMoneda = $dataTipoCambio[0]['simbolo'];
        $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
        $porcentaje = $_REQUEST['porcentaje'];
        $modo = $_REQUEST['modo'];
        $detalleOrdenVenta = new detalleOrdenVenta();
        $unidadMedida = $this->AutoLoadModel('unidadmedida');
        $detalleinventario = $this->AutoLoadModel('detalleinventario');
        $data = $detalleOrdenVenta->listaDetalleOrdenVenta($idGuia);
        $total = 0;
        for ($i = 0; $i < count($data); $i++) {
            echo "<tr>";
            if ($porcentaje != "") {
                if ($modo == 1) {
                    $precio = $data[$i]['preciolista'];
                    $data[$i]['preciolista'] = (($precio * $porcentaje) / 100);
                } else {
                    $cantidad = $data[$i]['cantsolicitada'];
                    $data[$i]['cantsolicitada'] = ceil(($cantidad * $porcentaje) / 100);
                }
            }
            $precioTotal = (($data[$i]['preciosolicitado']) * ($data[$i]['cantsolicitada']) - ($data[$i]['tipodescuento']));
            echo '<td>' . $data[$i]['codigopa'] . '</td>' .
                 '<td>' . $data[$i]['nompro'] . '</td>';
            $dataUnidad = $unidadMedida->buscaUnidadMedida($data[$i]['unidadmedida']);
            echo '<td>' . $dataUnidad[0]['codigo'] . '</td>' .
                    '<td><center>' . $detalleinventario->ultimabloqueasignadodelproducto($data[$i]['idproducto']) . '</center></td>' .
                    '<td align="center">' . $data[$i]['cantaprobada'] . '</td>' .
                    '<td align="center">' .
                    '<input type="text" name="DetalleOrdenVenta[' . $i . '][cantdespacho]" value="' . $data[$i]['cantaprobada'] . '" class="txtCantidadAprobada text-50 numeric" readonly >' .
                    '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][estado]" value="1" class="txtEstado">' .
                    '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][iddetalleordenventa]" value="' . $data[$i]['iddetalleordenventa'] . '">' .
                    '<input type="hidden" name="Producto[' . $i . '][idproducto]" value="' . $data[$i]['idproducto'] . '" class="valorIdProducto">' .
                    '<input type="hidden" name="Producto[' . $i . '][cantaprobada]" value="' . $data[$i]['cantaprobada'] . '" class="valorIdProducto">' .
                    '</td>' .
                    '<td><label class="Stock">' . $data[$i]["stockactual"] . '</label></td>' .
                    '<td><a href="#" class="btnEditar"><img src="/imagenes/editar.gif"></a></td>' .
                    '<td><a href="#" class="btnEliminar"><img src="/imagenes/eliminar.gif"></a></td>' .
                "</tr>";
            $total += $precioTotal;
        }
    }

    function listaDetalleDespacho() {
        $idGuia = $_REQUEST['id'];
        $dataGuia = $this->AutoLoadModel("OrdenVenta");
        $idTipoCambio = $dataGuia->BuscarCampoOVxId($idGuia, "IdTipoCambioVigente"); //PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA
        $TipoCambio = $this->AutoLoadModel("TipoCambio");
        $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
        $simboloMoneda = $dataTipoCambio[0]['simbolo'];
        $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
        $porcentaje = $_REQUEST['porcentaje'];
        $modo = $_REQUEST['modo'];
        $detalleOrdenVenta = new detalleOrdenVenta();
        $data = $detalleOrdenVenta->listaDetalleOrdenVenta($idGuia);
        $total = 0;
        for ($i = 0; $i < count($data); $i++) {
            echo "<tr>";
            if ($porcentaje != "") {
                if ($modo == 1) {
                    $precio = $data[$i]['preciolista'];
                    $data[$i]['preciolista'] = (($precio * $porcentaje) / 100);
                } else {
                    $cantidad = $data[$i]['cantsolicitada'];
                    $data[$i]['cantsolicitada'] = ceil(($cantidad * $porcentaje) / 100);
                }
            }
            $precioTotal = (($data[$i]['preciosolicitado']) * ($data[$i]['cantsolicitada']) - ($data[$i]['tipodescuento']));
            echo '<td>' . $data[$i]['codigopa'] . '</td>';
            echo '<td>' . $data[$i]['nompro'] . '</td>';
            echo '<td class="center">' .
                    '<input type="text" name="DetalleOrdenVenta[' . $i . '][cantdespacho]" value="' . $data[$i]['cantdespacho'] . '" class="txtCantidadAprobada text-50" readonly>' .
                    '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][estado]" value="1" class="txtEstado">' .
                    '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][iddetalleordenventa]" value="' . $data[$i]['iddetalleordenventa'] . '">' .
                 '</td>';
            echo '<td><textarea type="text" name="DetalleOrdenVenta[' . $i . '][serie]" value="' . $data[$i]['serie'] . '" style="margin: 0px; width: 400px; height: 47px;"></textarea></td>';
            echo "</tr>";
            $total += $precioTotal;
        }
    }

    function listaDetalleParaCobranzas() {
        $idGuia = $_REQUEST['id'];
        $dataGuia = $this->AutoLoadModel("OrdenVenta");
        $idTipoCambio = $dataGuia->BuscarCampoOVxId($idGuia, "IdTipoCambioVigente"); //PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA
        $idMoneda = $dataGuia->BuscarCampoOVxId($idGuia, "IdMoneda");
        $TipoCambio = $this->AutoLoadModel("TipoCambio");
        $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
        $simboloMoneda = $dataTipoCambio[0]['simbolo'];
        $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
        $detalleOrdenVenta = new detalleOrdenVenta();
        $data = $detalleOrdenVenta->listaDetalleOrdenVenta($idGuia);
        $totalGeneral = 0;
        $descuento = New Descuento();
        $dataDescuento = $descuento->listado();
        for ($i = 0; $i < count($dataDescuento); $i++) {
            $dscto[$dataDescuento[$i]['id']] = $dataDescuento[$i]['valor'];
        }
        for ($i = 0; $i < count($data); $i++) {
            if ($idMoneda == 2) {
                $data[$i]['preciolista'] = $data[$i]['preciolistadolares'];
            }
            //$precioneto=number_format((number_format($data[$i]['precioaprobado'],2)-($data[$i]['tdescuentoaprovado']/$data[$i]['cantaprobada'])),2);
            $precioneto = round($data[$i]['precioaprobado'] * (1 - $data[$i]['descuentoaprobadovalor']), 2);
            $precioTotal = $precioneto * $data[$i]['cantaprobada'];
            $total += $precioTotal;
            echo "<tr>";
            //$precioTotal=(($data[$i]['precioaprobado'])*($data[$i]['cantaprobada'])-($data[$i]['tdescuentoaprovado']));
            echo    '<td>' . $data[$i]['codigopa'] . '</td>';
            echo    '<td>' . $data[$i]['nompro'] . '</td>';
            echo    '<td class="center">' . $data[$i]['cantsolicitada'] . '</td>';
            echo    '<td class="center" style="background:#A49AB6">' . $data[$i]['cantaprobada'] . '</td>';
            echo    '<td class="center">' . $simboloMoneda . ' ' .
                        $data[$i]['preciolista'] .
                        '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][cantidad]" value="' . $data[$i]['cantaprobada'] . '">' .
                        '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][idproducto]" value="' . $data[$i]['idproducto'] . '">' .
                        '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][stockactual]" value="' . $data[$i]['stockactual'] . '">' .
                        //number_format($data[$i]['preciolista']/$TC_PrecioVenta,2)
                        '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][preciolista]" value="' . number_format($data[$i]['preciolista'] / $TC_PrecioVenta, 2) . '">' .
                        '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][total]" value="' . $precioTotal . '">' .
                        '<input type="hidden" name="Producto[' . $i . '][idproducto]" value="' . $data[$i]['idproducto'] . '" >' .
                        '<input type="hidden" name="Producto[' . $i . '][cantaprobada]" value="' . $data[$i]['cantaprobada'] . '" >' .
                    '</td>';
            echo    '<td class="right" >' . $simboloMoneda . ' ' . number_format($data[$i]['precioaoferta'] / $TC_PrecioVenta, 2) . '</td>';
            echo    '<td class="right" style="background:#A49AB6">' . $simboloMoneda . ' ' . number_format($data[$i]['precioaprobado'], 2) . '</td>';
            echo    '<td>' . $data[$i]['descuentoaprobadotexto'] . '</td>';
            echo    '<td class="right" style="background:#A49AB6">' . $simboloMoneda . ' ' . $precioneto . '</td>';
            echo    '<td class="right">' . $simboloMoneda . ' ' . $precioTotal .
                    '</td>';
            echo "</tr>";
            $totalGeneral += $precioTotal;
        }
        echo '<tr class="red">' .
                '<td colspan="9" class="right">Total:' .
                '<td class="right">' . $simboloMoneda . '  ' . number_format($totalGeneral, 2, '.', '') .
                    '<input type="hidden" name="ordenVenta[importeordencobro]" value="' . $totalGeneral . '">' .
                '</td>' .
             '</tr>';
    }

    function listaDetalleParaCreditos() {
        $idGuia = $_REQUEST['id'];
        $dataGuia = $this->AutoLoadModel("OrdenVenta");
        $idTipoCambio = $dataGuia->BuscarCampoOVxId($idGuia, "IdTipoCambioVigente"); //PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA
        $TipoCambio = $this->AutoLoadModel("TipoCambio");
        $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
        $simboloMoneda = $dataTipoCambio[0]['simbolo'];
        $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
        $detalleOrdenVenta = new detalleOrdenVenta();
        $data = $detalleOrdenVenta->listaDetalleOrdenVenta($idGuia);
        $totalGeneral = 0;
        $descuento = New Descuento();
        $dataDescuento = $descuento->listado();
        for ($i = 0; $i < count($dataDescuento); $i++) {
            $dscto[$dataDescuento[$i]['id']] = $dataDescuento[$i]['valor'];
        }
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['idmoneda'] == 2) {
                $data[$i]['preciolista'] = $data[$i]['preciolistadolares'];
            }
            //$precioneto=number_format((number_format($data[$i]['precioaprobado'],2)-($data[$i]['tdescuentoaprovado']/$data[$i]['cantaprobada'])),2);
            $precioneto = $data[$i]['preciofinal'];
            $precioTotal = $precioneto * $data[$i]['cantdespacho'];
            $total += round($precioTotal, 2);
            echo "<tr>";
            //$precioTotal=(($data[$i]['precioaprobado'])*($data[$i]['cantaprobada'])-($data[$i]['tdescuentoaprovado']));
            echo    '<td>' . $data[$i]['codigopa'] . '</td>';
            echo    '<td>' . $data[$i]['nompro'] . '</td>';
            echo    '<td class="center">' . $data[$i]['cantsolicitada'] . '</td>';
            echo    '<td class="center" style="background:#A49AB6">' . $data[$i]['cantdespacho'] . '</td>';
            echo    '<td class="center">' . $simboloMoneda . '' .
                        number_format($data[$i]['preciolista'] / $TC_PrecioVenta, 2) .
                        '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][cantidad]" value="' . $data[$i]['cantdespacho'] . '">' .
                        '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][idproducto]" value="' . $data[$i]['idproducto'] . '">' .
                        '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][stockactual]" value="' . $data[$i]['stockactual'] . '">' .
                        '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][pu]" value="' . round($data[$i]['preciofinal'], 2) . '">' .
                        '<input type="hidden" name="DetalleOrdenVenta[' . $i . '][total]" value="' . $precioTotal . '">' .
                        '<input type="hidden" name="Producto[' . $i . '][idproducto]" value="' . $data[$i]['idproducto'] . '" >' .
                        '<input type="hidden" name="Producto[' . $i . '][cantdespacho]" value="' . $data[$i]['cantdespacho'] . '" >' .
                    '</td>';
            echo    '<td class="right" >' . $simboloMoneda . '' . number_format($data[$i]['precioaprobado'], 2) . '</td>';
            echo    '<td>' . $data[$i]['descuentoaprobadotexto'] . '</td>';
            echo    '<td>' . ($data[$i]['descuentooferta'] == 0 ? ' - ' : round($data[$i]['descuentooferta'], 2) . '%') . '</td>';
            echo    '<td class="right" style="background:#A49AB6">' . $simboloMoneda . '' . number_format($precioneto, 2) . '</td>';
            echo    '<td class="right">' . $simboloMoneda . '' . $precioTotal . '</td>';
            echo "</tr>";
            $totalGeneral += $precioTotal;
        }
        echo '<tr class="red">' .
                '<td colspan="8" class="right">Total:' .
                '<td class="right">' . $simboloMoneda . ' ' . number_format($totalGeneral, 2) .
                    '<input id="importetotal" type="hidden" name="ordenVenta[importeordencobro]" value="' . $totalGeneral . '">' .
                '</td>' .
            '</tr>';
    }

    function listaxVendedor() {
        $filtro = $_REQUEST['filtro'];
        $fecha = $_REQUEST['fecha'];
        $fechaInicio = $_REQUEST['fechaInicio'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        $ordenVenta = new OrdenVenta();
        $datos = $ordenVenta->listarxvendedor2($filtro, $fecha, $fechaInicio, $fechaFinal);
        for ($i = 0; $i < count($datos); $i++) {
            if ($data[$i]['idmoneda'] == 2) {
                $data[$i]['preciolista'] = $data[$i]['preciolistadolares'];
            }
            $vbVentas = $datos[$i]['vbventas'];
            $vbCobranza = $datos[$i]['vbcobranzas'];
            $vbCreditos = $datos[$i]['vbcreditos'];
            $vbAlmacen = $datos[$i]['vbalmacen'];
            echo "<tr>";
            echo    "<td>" . $datos[$i]['codigov'] . "</td>";
            echo    "<td>" . $datos[$i]['importeov'] . "</td>";
            echo    '<td>' . date("d/m/Y", strtotime($datos[$i]['fordenventa'])) . '</td>';
            echo    "<td>" . $datos[$i]['razonsocial'] . "</td>";
            echo    "<td>" . $datos[$i]['nombrezona'] . "</td>";
            echo    "<td>" . $datos[$i]['duraciontotal'] . "</td>";
            echo    "<td>" . $datos[$i]['direccion'] . "</td>";
            $ruta = "/imagenes/iconos/";
            $imagen1 = ($vbVentas == -1) ? $ruta . 'pendiente.jpg' : (($vbVentas == 1) ? $ruta . 'aprobado.jpg' : $ruta . 'desaprobado.jpg');
            $imagen2 = ($vbCobranza == -1) ? $ruta . 'pendiente.jpg' : (($vbCobranza == 1) ? $ruta . 'aprobado.jpg' : $ruta . 'desaprobado.jpg');
            $imagen3 = ($vbCreditos == -1) ? $ruta . 'pendiente.jpg' : (($vbCreditos == 1) ? $ruta . 'aprobado.jpg' : $ruta . 'desaprobado.jpg');
            $imagen4 = ($vbAlmacen == -1) ? $ruta . 'pendiente.jpg' : (($vbAlmacen == 1) ? $ruta . 'aprobado.jpg' : $ruta . 'desaprobado.jpg');
            echo    '<td><img src="' . $imagen1 . '"</td>';
            echo    '<td><img src="' . $imagen2 . '"</td>';
            echo    '<td><img src="' . $imagen3 . '"</td>';
            echo    '<td><img src="' . $imagen4 . '"</td>';
            echo    '<td width="100px">' .
                        '<a href="/ordenventa/detalle/' . $datos[$i]['idordenventa'] . '" class="btnDetalleOrdenVenta"><img src="/imagenes/detalle.jpg"></a>' .
                        '<a href="/almacen/editar/"' . $datos[$i]['idordenventa'] . '" class="btnEditarAlmacen"><img src="/imagenes/iconos/editar.gif"></a>' .
                        '<a href="/almacen/eliminar/"' . $datos[$i]['idordenventa'] . '" class="btnEliminarAlmacen"><img src="/imagenes/iconos/eliminar.gif"></a>';
            echo    "</td>";
            echo "</tr>";
            echo "</tr>";
        }
    }

    function grabaCondicionLetra() {
        $condicionletra = new CondicionLetra();
        $data = $_REQUEST['CondicionLetra'];
        $exito = $condicionletra->grabaCondicionLetra($data);
        if ($exito) {
            $ruta['ruta'] = $_REQUEST['origen'];
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function PendientesxPagar() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaOrdenxPagar2($texIni);
        echo json_encode($data);
    }

    function PendientesxPagar2() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaOrdenxPagar2($texIni);
        echo json_encode($data);
    }

    function OrdeventaDespachados() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaOrdenVentaDespacho($texIni);
        echo json_encode($data);
    }

    function busquedaletrasxov() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaNumeroLetraxOV($texIni);
        echo json_encode($data);
    }

    function listadeletrasxov() {
        $idordenventa = $_REQUEST['idordenventa'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaListaNumeroLetraxidOV($idordenventa);
        echo json_encode($data);
    }

    function busquedaletras() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaOrdenxNumeroLetra($texIni);
        echo json_encode($data);
    }

    function guiar() {
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $data['esguiado'] = $_REQUEST['esguiado'];
        $idov = $_REQUEST['idov'];
        $exito = $ordenventa->actualizaOrdenVenta($data, $idov);
    }

    function actualizarobservacionesfacturacion() {
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $data['mventas'] = $_REQUEST['observaciones'];
        $idov = $_REQUEST['idov'];
        $exito = $ordenventa->actualizaOrdenVenta($data, $idov);
    }

    /*
      Detalle para Guia Madre:
     */
    function ListaGuiaMadre() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaOrdenxPagar($texIni);
        echo json_encode($data);
    }

    function ListaGuiaMadreConDevolucionFactura() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaOrdenConDevolucionFactura($texIni);
        echo json_encode($data);
    }

    function ListaGuiaMadreConFactura() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaOrdenConFactura($texIni);
        echo json_encode($data);
    }

    function CabeceraGuiaMadre() {
        $idGuia = $_REQUEST['id'];
        $cliente = New Cliente();
        $actorRol = New actorRol();
        $dataCliente = $cliente->buscaxOrdenVenta($idGuia);
        $iddespachador = $dataCliente[0]['iddespachador'];
        $idverificador = $dataCliente[0]['idverificador'];
        $idverificador2 = $dataCliente[0]['idverificador2'];
        $idvendedor = $dataCliente[0]['idvendedor'];
        $dataDespachador = $actorRol->buscaActorxRol($iddespachador);
        $dataVerificador = $actorRol->buscaActorxRol($idverificador);
        $dataVerificador2 = $actorRol->buscaActorxRol($idverificador2);
        $dataVendedor = $actorRol->buscaActorxRol($idvendedor);
        echo "<tr><th colspan=\"8\"><h2>Guia Madre Nro: " . $dataCliente[0]['codigov'] . "</h2></th></tr>";
        echo "<tr>
                <th>Sr.(s) :</th>
                <td colspan=\"5\">" . $dataCliente[0]['codantiguo'] . " " . $dataCliente[0]['razonsocial'] . "</td>
                <th>Nro RUC:</th>
                <td>" . $dataCliente[0]['ruc'] . "</td>
             </tr>
             <tr>
                <th>Dirección: </th><td colspan=\"3\">" . $dataCliente[0]['direccion_envio'] . "</td>
                <th>Dirección Despacho: </th><td colspan=\"3\">" . $dataCliente[0]['direccion_despacho'] . "</td>
             </tr>
             <tr>
                <th>Lugar: </th><td colspan=\"7\">" . $dataCliente[0]['nombredistrito'] . '-' . $dataCliente[0]['nombreprovincia'] . '-' . $dataCliente[0]['nombredepartamento'] . "</td>
             </tr>
             <tr>
                <th>Telf. / Cel. / Correo :</th><td colspan='3'>" . $dataCliente[0]['telefono'] . " / " . $dataCliente[0]['celular'] . " <br> " . $dataCliente[0]['email'] . " - " . $dataCliente[0]['email2'] . "</td>
                <th>Situacion</th><td>" . $dataCliente[0]['situacion'] . "</td>
                <th>Emisión</td><td>" . date('Y-m-d', strtotime($dataCliente[0]['fordenventa'])) . "</td>
             </tr>
             <tr>
                <th>Atención :</th><td>" . $dataCliente[0]['contacto'] . "</td>
                <th>Ag. Transp. :</th><td>" . $dataCliente[0]['razonsocialtransp'] . " (<b>Telf: </b>" . $dataCliente[0]['telfonotransp'] . ")</td>
                <th>Porcentaje <br>Comision</th><td>" . $dataCliente[0]['porComision'] . "</td>
                <th>Despacho</th><td>" . $dataCliente[0]['fechadespacho'] . "</td>
             </tr>
             <tr>
                <th>Vendedor :</th><td colspan=\"3\" title='" . $dataVendedor[0]['nombres'] . ' ' . $dataVendedor[0]['apellidopaterno'] . ' ' . $dataVendedor[0]['apellidomaterno'] . "'>#" . $idvendedor . "</td>
                <th>$nbsp</th><td>$nbsp</td>
                <th>Vence:</td><td>" . $dataCliente[0]['fechavencimiento'] . "</td>
             </tr>	
             <tr>
                <th>Condiciones :</th><td colspan=\"5\">" . (html_entity_decode($dataCliente[0]['condiciones'], ENT_QUOTES)) . "</td>
                <th>Re-Chequeador :</th>
                <td>" . $dataVerificador2[0]['nombres'] . ' ' . $dataVerificador2[0]['apellidopaterno'] . ' ' . $dataVerificador2[0]['apellidomaterno'] . "</td>
             </tr>
             <tr>
                <th>N° Cajas :</th>
                <td >" . $dataCliente[0]['nrocajas'] . "</td>
                <th>N° Bultos:</th>
                <td >" . $dataCliente[0]['nrobultos'] . "</td>
                <th>Despachador:</th>
                <td >" . $dataDespachador[0]['nombres'] . ' ' . $dataDespachador[0]['apellidopaterno'] . ' ' . $dataDespachador[0]['apellidomaterno'] . "</td>
                <th>Verificador:</th>
                <td >" . $dataVerificador[0]['nombres'] . ' ' . $dataVerificador[0]['apellidopaterno'] . ' ' . $dataVerificador[0]['apellidomaterno'] . "</td>
             </tr>
             <tr>
		<th>Observaciones</th>";
        if ($_SESSION['idrol'] == 79) {
            echo "<td style='text-align:left;' id=\"idThObservaciones\" data-on='0' colspan=\"7\">" .
                    '<span id="editObservaciones" title="Editar observacion">' . $dataCliente[0]['mventas'] . ' <span id="titleObservaciones">[ Editar ]</span></span>' .
                    '<input type="text" id="idObservacionesFacturacion" data-idguia="' . $idGuia . '" value="' . $dataCliente[0]['mventas'] . '">' .
                    '<img width="21" id="guardarObservaciones" title="Guardar observacion" heigth="21" src="/imagenes/grabar.gif">' .
                 "</td>";
        } else {
            echo "<td style='text-align:left;' colspan=\"7\">" .
                    $dataCliente[0]['mventas'] .
                 "</td>";
        }
        echo "</tr>
	      <tr>
		<td colspan=\"8\"><b>Remitimos a Ud(s) en buenas condiciones lo siguiente:</b></td>
              </tr>";
    }

    function DetalleGuiaMadre() {
        
        $idGuia = $_REQUEST['id'];
        $dataGuia = $this->AutoLoadModel("OrdenVenta");
        $idTipoCambio = $dataGuia->BuscarCampoOVxId($idGuia, "IdTipoCambioVigente"); //PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA
        $TipoCambio = $this->AutoLoadModel("TipoCambio");
        $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
        $simboloMoneda = $dataTipoCambio[0]['simbolo'];
        $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
        $detalleOrdenVenta = new detalleOrdenVenta();
        $descuento = New Descuento();
        $dataDescuento = $descuento->listadoTotal();
        for ($i = 0; $i < count($dataDescuento); $i++) {
            $dscto[$dataDescuento[$i]['id']] = $dataDescuento[$i]['valor'];
        }
        $data = $detalleOrdenVenta->listaDetalleOrdenVentaGuia($idGuia);
        $total = 0;
        echo "<tr><th colspan=9><h3>DETALLE DE PRODUCTOS DE LA ORDEN DE PEDIDO (No incluye Gastos Adicionales)</h3></th></tr>";
        $cantidad = count($data);
        $ofertaExiste=0;
        for($i = 0; $i < $cantidad; $i++){
            if($data[$i]['descuentooferta']>0){
                $ofertaExiste=1;
            }
        }
        if($ofertaExiste==1){
            echo "<tr>
                    <th>Cant.</th> 
                    <th>U.Med.</th> 
                    <th>Código.</th> 
                    <th>Descripción.</th>
                    <th>Precio</th>
                    <th>%Descto.</th>
                    <th style='width:70'>%Descto. Oferta</th>
                    <th>P/Final.</th>
                    <th>Monto.</th>
                 </tr>";
                for ($i = 0; $i < $cantidad; $i++) {
                $precioneto = number_format($data[$i]['precioaprobado'] * (1 - $data[$i]['descuentoaprobadovalor']), 2);
                $precioTotal = $data[$i]['preciofinal'] * $data[$i]['cantdespacho'];
                $total += $precioTotal;
                echo "<tr>";
                echo    '<td>' . $data[$i]['cantdespacho'] . '</td>'; 
                echo    '<td>' . $data[$i]['unidadmedida'] . '</td>';
                echo    '<td>' . $data[$i]['codigopa'] . '</td>';
                echo    '<td>' . $data[$i]['nompro'] . '</td>';
                echo    '<td>' . ' ' . $simboloMoneda . ' ' . number_format($data[$i]['precioaprobado'], 2) . '</td>';
                echo    '<td>' . round($dscto[$data[$i]['descuentoaprobado']], 2) . '</td>';
                echo    $data[$i]['descuentooferta']==0? '<td style="text-align:right;">--</td>':'<td style="text-align:right;">'.bcdiv($data[$i]['descuentooferta'],1,2).'%</td>';            
                
                // FiltroPreciofinal
                    $arr1 = str_split($data[$i]['preciofinal']);
                        for($j=0;$j<count($arr1);$j++){
                            if($arr1[$j]=='.'){
                                $indice=$j;
                            }
                        }
                        if($arr1[$indice+3]=='0' || $arr1[$indice+3]==''){
                            echo    '<td style="text-align:right;">' . ' ' . $simboloMoneda . ' ' . number_format($data[$i]['preciofinal'], 2) . '</td>';
                        } else {
                            echo    '<td style="text-align:right;">' . ' ' . $simboloMoneda . ' ' . number_format($data[$i]['preciofinal'], 3) . '</td>';
                        }
                // FiltroPreciofinal End
                            
                //Filtro
                $arr1 = str_split($precioTotal);
                    for($j=0;$j<count($arr1);$j++){
                        if($arr1[$j]=='.'){
                            $indice=$j;
                        }
                    }
                    if($arr1[$indice+3]=='0' || $arr1[$indice+3]==''){
                        echo    '<td style="text-align:right;">' . ' ' . $simboloMoneda . ' ' . number_format($precioTotal, 2) . '</td>';
                    } else {
                        echo    '<td style="text-align:right;">' . ' ' . $simboloMoneda . ' ' . number_format($precioTotal, 3) . '</td>';
                    }
                //Filtro End
                echo "</tr>";
            }
            echo '<tr style="color:#f00">';
            echo    '<td colspan="8" class="right bold">Total a Pagar</td>';
            echo    '<td class="right"><input type="text" id="txtTotal" value="' . ' ' . $simboloMoneda . ' ' . number_format(($total), 2) . '" class="text-100 right" readonly></td>';
            echo "</tr>";
            $ordenGasto = $this->AutoLoadModel('ordengasto');
            $Percepcion = $ordenGasto->importeGasto($idOrdenVenta, 6);
            if (!empty($Percepcion)) {
                echo '<tr styl                                                                                                                                                                                                                                                                                                                                                                                                                              e="color:#f00">';
                echo    '<td colspan="8" class="righ t bold">Total a Pagar (+Percepción)</td>';
                echo    '<td class="right"><b>' . ' ' . $simboloMoneda . ' ' . number_format(($Percepcion + $total), 2) . '</b></td>';
                echo "</tr>";
            }
            echo '<tr><td colspan="9" style="background:white;">&nbsp;</td></tr>';
            for ($i = 0; $i < $cantidad; $i++) {
                if ($data[$i]['serie'] != "") {
                    echo "<tr>";
                    echo    '<th>' . $data[$i]['codigopa'] . '</th>';
                    echo    '<th>SERIES: </th>';
                    echo    '<td colspan="7">' . $data[$i]['serie'] . '</td>';
                    echo "</tr>";
                }
            }
        } else {
            echo "<tr>
                    <th>Cant.</th>
                    <th>U.Med.</th>
                    <th>Código.</th>
                    <th colspan='2'>Descripción.</th>
                    <th>Precio</th>
                    <th>%Descto.</th>                
                    <th>P/Final.</th>
                    <th>Monto.</th>
                 </tr>";
            
                for ($i = 0; $i < $cantidad; $i++) {
                $precioneto = number_format($data[$i]['precioaprobado'] * (1 - $data[$i]['descuentoaprobadovalor']), 2);
                $precioTotal = $data[$i]['preciofinal'] * $data[$i]['cantdespacho'];
                $total += $precioTotal;
                echo "<tr>";
                echo    '<td>' . $data[$i]['cantdespacho'] . '</td>';
                echo    '<td>' . $data[$i]['unidadmedida'] . '</td>';
                echo    '<td>' . $data[$i]['codigopa'] . '</td>';
                echo    '<td colspan="2">' . $data[$i]['nompro'] . '</td>';
                echo    '<td>' . ' ' . $simboloMoneda . ' ' . number_format($data[$i]['precioaprobado'], 2) . '</td>';
                echo    '<td>' . round($dscto[$data[$i]['descuentoaprobado']], 2) . '</td>';
                // FiltroPreciofinal
                    $arr1 = str_split($data[$i]['preciofinal']);
                        for($j=0;$j<count($arr1);$j++){
                            if($arr1[$j]=='.'){
                                $indice=$j;
                            }
                        }
                        if($arr1[$indice+3]=='0' || $arr1[$indice+3]==''){
                            echo    '<td style="text-align:right;">' . ' ' . $simboloMoneda . ' ' . number_format($data[$i]['preciofinal'], 2) . '</td>';
                        } else {
                            echo    '<td style="text-align:right;">' . ' ' . $simboloMoneda . ' ' . number_format($data[$i]['preciofinal'], 3) . '</td>';
                        }
                // FiltroPreciofinal End
                // 
                // 
                //Filtro
                $arr2 = str_split($precioTotal);
                    for($j=0;$j<count($arr2);$j++){
                        if($arr2[$j]=='.'){
                            $indice=$j;
                        }
                    }
                    if($arr2[$indice+3]=='0' || $arr2[$indice+3]==''){
                        echo    '<td style="text-align:right;">' . ' ' . $simboloMoneda . ' ' . number_format($precioTotal, 2) . '</td>';
                    } else {
                        echo    '<td style="text-align:right;">' . ' ' . $simboloMoneda . ' ' . number_format($precioTotal, 3) . '</td>';
                    }
                //Filtro End
                echo "</tr>";
            }
            echo '<tr style="color:#f00">';
            echo    '<td colspan="8" class="right bold">Total a Pagar</td>';
            echo    '<td class="right"><input type="text" id="txtTotal" value="' . ' ' . $simboloMoneda . ' ' . number_format(($total), 2) . '" class="text-100 right" readonly></td>';
            echo "</tr>";
            $ordenGasto = $this->AutoLoadModel('ordengasto');
            $Percepcion = $ordenGasto->importeGasto($idOrdenVenta, 6);
            if (!empty($Percepcion)) {
                echo '<tr style="color:#f00">';
                echo    '<td colspan="7" class="right bold">Total a Pagar (+Percepción)</td>';
                echo    '<td class="right"><b>' . ' ' . $simboloMoneda . ' ' . number_format(($Percepcion + $total), 2) . '</b></td>';
                echo "</tr>";
            }
            echo '<tr><td colspan="9" style="background:white;">&nbsp;</td></tr>';
            for ($i = 0; $i < $cantidad; $i++) {
                if ($data[$i]['serie'] != "") {
                    echo "<tr>";
                    echo    '<th>' . $data[$i]['codigopa'] . '</th>';
                    echo    '<th>SERIES: </th>';
                    echo    '<td colspan="7">' . $data[$i]['serie'] . '</td>';
                    echo "</tr>";
                }
            }
            
        }
    }

    /*
      Edicion de Orden de Venta
     */
    function EdicionGlobal() {
        //Capturamos el ID de la Orden de Venta
        $idOrdenVenta = $_REQUEST['id'];
        //Cargamos los modelos de datos:
        $OBJ_ordenVenta = $this->AutoLoadModel("OrdenVenta");
        $dataOrdenVenta = $OBJ_ordenVenta->DataCompletaOrdenVentaxId($idOrdenVenta);
        $filaOrdenVenta = $dataOrdenVenta[0];
        echo "<input type=\"hidden\" id=\"idOrdenVenta\" name=\"OrdenVenta[idOrdenVenta]\" value=\"" . $filaOrdenVenta['idordenventa'] . "\" >
              <input type=\"hidden\" id=\"idCliente\" name=\"OrdenVenta[idcliente]\" value=\"" . $filaOrdenVenta['idcliente'] . "\">		
		<tr>
                    <th colspan=\"4\"><h2> Orden de Venta Nro : " . $filaOrdenVenta['NroOrdenVenta'] . "</h2></th>
		</tr>
		<tr><th>Fecha Orden venta </th><th>Razon Social</th><th>Transporte</th><th>Vendedor</th></tr>
		<tr>
                    <td>
                        <input class=\"datepicker\" type=\"text\"   id=\"fecha\" value=\"" . $filaOrdenVenta['fordenventa'] . "\"  readonly>
                    </td>
                    <td>
                        <input type=\"text\"   id=\"razonsocial\" value=\"" . $filaOrdenVenta['razonsocial'] . "\" size=\"100\">
                    </td>
                    <td>
                        <input type=\"text\" readonly disabled id=\"razonsocial\" value=\"" . $filaOrdenVenta['trazonsocial'] . "\">
                    </td>
                    <td>
                        <input type=\"text\" readonly disabled id=\"razonsocial\" value=\"" . $filaOrdenVenta['vendedor'] . "\">
                    </td>
		</tr>";
    }

    /* Estado de Guia */
    function ListaEstadoGuia() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaOrdenxPagarEstadoLetra($texIni);
        echo json_encode($data);
    }

    function CabeceraEstadoGuia() {
        $idGuia = $_REQUEST['id'];
        $dataGuia = $this->AutoLoadModel("OrdenVenta");
        $idTipoCambio = $dataGuia->BuscarCampoOVxId($idGuia, "IdTipoCambioVigente"); //PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA
        $TipoCambio = $this->AutoLoadModel("TipoCambio");
        $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
        $simboloMoneda = $dataTipoCambio[0]['simbolo'];
        $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
        $cliente = New Cliente();
        $actorRol = New actorRol();
        $tipocobro = $this->AutoLoadModel('tipocobranza');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $ordenCobro = $this->AutoLoadModel('ordencobro');
        $datatipocobro = $tipocobro->lista();
        $dataCliente = $cliente->buscaxOrdenVenta($idGuia);
        $iddespachador = $dataCliente[0]['iddespachador'];
        $idverificador = $dataCliente[0]['idverificador'];
        $idverificador2 = $dataCliente[0]['idverificador2'];
        $idvendedor = $dataCliente[0]['idvendedor'];
        $dataDespachador = $actorRol->buscaActorxRol($iddespachador);
        $dataVerificador = $actorRol->buscaActorxRol($idverificador);
        $dataVerificador2 = $actorRol->buscaActorxRol($idverificador2);
        $dataVendedor = $actorRol->buscaActorxRol($idvendedor);
        $totalGuia = $ordenGasto->totalGuia($idGuia);
        $totalRenovados = $ordenGasto->importeGasto($idGuia, 1);
        $totalFlete = $ordenGasto->importeGasto($idGuia, 3);
        $totalGastoBancario = $ordenGasto->importeGasto($idGuia, 4);
        $totalEnvioSobre = $ordenGasto->importeGasto($idGuia, 5);
        $totalCostoMantenimiento = $ordenGasto->importeGasto($idGuia, 8);
        $totalGastoProtesto = $ordenGasto->importeGasto($idGuia, 2);
        $totalPercepcion = $ordenGasto->importeGasto($idGuia, 6);
        $totalLAdi = $ordenGasto->importeGasto($idGuia, 10);
        $totalAnulado = $ordenCobro->totalAnulado($idGuia);
        $totalDevolucion = $dataCliente[0]['importedevolucion'];
        $importeov = $dataCliente[0]['importeov'];
        $valorComision = (($importeov - $totalDevolucion) * $dataCliente[0]['porComision']) / 100;
        if ($dataCliente[0]['escomisionado'] == 1) {
            $grabar = $dataCliente[0]['porComision'] . "% &nbsp" . "<b style='color:white;background-color:green;font-size:12px;'>Pagado</b>";
        } elseif (strcmp($dataCliente[0]['situacion'], "Pendiente") == 0) {
            $grabar = $dataCliente[0]['porComision'] . "% &nbsp" . "<b style='color:white;background-color:green;font-size:12px;'></b>";
        } elseif (strcmp($dataCliente[0]['situacion'], "cancelado") == 0) {
            $grabar = "<input size='1' class='numeric' id='txtPorcentaje' type='text' readonly value='" . $dataCliente[0]['porComision'] . "'>%  <a id='editarPorcentaje' href='#'><img width='20'  src='/imagenes/editar.gif'></a> &nbsp <a id='grabarPorcentaje' href='#'><img width='25'  src='/imagenes/grabar.gif'></a>";
        }
        echo "<input type='hidden' id='idordenventa' value='" . $dataCliente[0]['idordenventa'] . "'>";
        echo "<tr><th colspan=\"9\"><h2>Estado Guia Nro: " . $dataCliente[0]['codigov'] . "</h2></th></tr>";
        echo "<tr>
                <th>Sr.(s) :</th>
                <td colspan=\"4\">" . $dataCliente[0]['codantiguo'] . " " . $dataCliente[0]['razonsocial'] . "</td>
                <th>Teléfono :</th><td>" . $dataCliente[0]['telefono'] . "</td>
                <th>Nro RUC:</th>
                <td>" . $dataCliente[0]['ruc'] . "</td>
             </tr>
             <tr>
                <th>Dirección: </th><td colspan=\"2\">" . $dataCliente[0]['direccion_envio'] . "</td>
                <th>Dirección Despacho: </th><td colspan=\"3\">" . $dataCliente[0]['direccion_despacho'] . "</td>
                <th>Lugar: </th><td colspan=\"2\">" . $dataCliente[0]['nombredistrito'] . '-' . $dataCliente[0]['nombreprovincia'] . '-' . $dataCliente[0]['nombredepartamento'] . "</td>
             </tr>
             <tr>
                <th>Atención :</th><td colspan='2'>" . $dataCliente[0]['contacto'] . "</td>
                <th>Horario Atte. :</th><td >" . $dataCliente[0]['horarioatencion'] . "</td>
                <th>Ag. Transp. :</th><td colspan='3'>" . $dataCliente[0]['razonsocialtransp'] . "</td>
             </tr>
                <th >Despachador:</th>
                <td colspan=\"2\">" . $dataDespachador[0]['nombres'] . ' ' . $dataDespachador[0]['apellidopaterno'] . ' ' . $dataDespachador[0]['apellidomaterno'] . "</td>					
                <th>Vendedor :</th><td >" . $dataVendedor[0]['nombres'] . ' ' . $dataVendedor[0]['apellidopaterno'] . ' ' . $dataVendedor[0]['apellidomaterno'] . "</td>
                <th >Condiciones :</th>
                <td colspan=\"3\">" . (html_entity_decode($dataCliente[0]['condiciones'], ENT_QUOTES)) . "</td>
             </tr>
             <!--
             <tr >
                <th>Re-Chequeador:</th>
                <td >" . $dataVerificador2[0]['nombres'] . ' ' . $dataVerificador2[0]['apellidopaterno'] . ' ' . $dataVerificador2[0]['apellidomaterno'] . "</td>
                <th>Verificador:</th>
                <td >" . $dataVerificador[0]['nombres'] . ' ' . $dataVerificador[0]['apellidopaterno'] . ' ' . $dataVerificador[0]['apellidomaterno'] . "</td>
                <th>N° Cajas :</th>
                <td >" . $dataCliente[0]['nrocajas'] . "</td>
                <th>N° Bultos:</th>
                <td >" . $dataCliente[0]['nrobultos'] . "</td>
             </tr>
             -->
             <tr>
                <th>Emisión</td><td >" . date('Y-m-d', strtotime($dataCliente[0]['fordenventa'])) . "</td>
                <th>Despacho</td><td>" . $dataCliente[0]['fechadespacho'] . "</td>
                <th>Vence:</td><td colspan=\"2\">" . $dataCliente[0]['fechavencimiento'] . "</td>
                <th >Condiciones :</th><td>" . (html_entity_decode($dataCliente[0]['condiciones'], ENT_QUOTES)) . "</td>
             </tr>
             <tr>
                <th colspan='7' style='background:white;color:white;font-size:15;'>&nbsp;</td>
                <th >Importe Orden Venta</th><td style='background:yellow;color:black;font-size:15;'>" . $simboloMoneda . " " . number_format($dataCliente[0]['importeov'], 2) . "</td>
             </tr>
             <tr><td style='background:white;' colspan='9'>&nbsp;</td></tr>
             <tr>
                <th>Total Renovacion</th><td>" . $simboloMoneda . "  " . number_format($totalRenovados, 2) . "</td>
                <th>Total Protesto</th><td>" . $simboloMoneda . " " . number_format($totalGastoProtesto, 2) . "</td>
                <th>Total Flete</th><td colspan=\"2\">" . $simboloMoneda . " " . number_format($totalFlete, 2) . "</td>
                <th>Percepcion</th><td >" . $simboloMoneda . "  " . number_format($totalPercepcion, 2) . "</td>
             </tr>
             <tr>
                <th>Total Envio Sobre</th><td>" . $simboloMoneda . " " . number_format($totalEnvioSobre, 2) . "</td>
                <th>Total Gasto Bancario</th><td>" . $simboloMoneda . "  " . number_format($totalGastoBancario, 2) . "</td>
                <th>Total Costo Mantenimiento</th><td colspan=\"2\">" . $simboloMoneda . " " . number_format($totalCostoMantenimiento, 2) . "</td>
                <th>Letras Adicionales </th><td>" . $simboloMoneda . " " . number_format($totalLAdi, 2) . "</td>
             </tr>
             <tr>
                <th colspan='7' style='background:white;color:white;font-size:15;'>&nbsp;</td>
                <th>Gastos Adicionales</th><td style='background:yellow;color:black;font-size:15;'>" . $simboloMoneda . " " . number_format($totalFlete + $totalCostoMantenimiento + $totalEnvioSobre + $totalGastoBancario + $totalPercepcion, 2) . "</td>
             </tr>
             <tr><td style='background:white;' colspan='9'>&nbsp;</td></tr>
             <tr>
                <th colspan='5' style='background:white;color:white;font-size:15;'>&nbsp;</td>
                <th colspan='3'>Total Guia (Importe Pedido + Gastos adicionales)</th><td style='background:yellow;color:black;font-size:15;'>" . $simboloMoneda . " " . number_format($totalGuia, 2) . "</td>
             </tr>
             <tr><td style='background:white;' colspan='9'>&nbsp;</td></tr>
             <tr><td style='background:white;' colspan='9'>&nbsp;</td></tr>
             <tr>
                <th>Total<br>Anulado</th><td>" . $simboloMoneda . "  " . number_format($totalAnulado, 2) . "</td>
                <th>Total Devoluciones</th><td>" . $simboloMoneda . "  " . number_format($totalDevolucion, 2) . "</td>										
                <th>Total Pagado</th><td colspan=\"2\">" . $simboloMoneda . " " . number_format($dataCliente[0]['importepagado'], 2) . "</td>
                <th>Total Deuda</th><td style='background:yellow;color:black;font-size:15;'>" . $simboloMoneda . " " . number_format(($totalGuia - $dataCliente[0]['importepagado']), 2) . "</td>
             </tr>
             <tr>
                <th>Tipo Cobranza :</th>
                <td>
                    <select id='lsttipocobranza'><option value=''>No Asignado</option>";
        $cantidadtipocobro = count($datatipocobro);
        for ($i = 0; $i < $cantidadtipocobro; $i++) {
            if ($datatipocobro[$i]['idtipocobranza'] == $dataCliente[0]['idtipocobranza']) {
                echo "<option selected value=" . $datatipocobro[$i]['idtipocobranza'] . ">" . $datatipocobro[$i]['nombre'] . "</option>";
            } else {
                echo "<option value=" . $datatipocobro[$i]['idtipocobranza'] . ">" . $datatipocobro[$i]['nombre'] . "</option>";
            }
        }
        echo "</select>
		&nbsp <a href='' id='grabartipocobranza'><img href='' width='25' heigth='25' src='/imagenes/grabar.gif'></a>
             </td>			
                <th>Porcentaje <br>Comision</th><td>" . $grabar . "</td>
                <th>Importe<br>Comision</th><td colspan=\"2\">" . $simboloMoneda . "  " . number_format(($valorComision), 2) . "</td>
                <th>Situacion</th><td  style='background:green;color:black;font-size:15;'>" . $dataCliente[0]['situacion'] . "</td>
             </tr>	
             <tr><td colspan='8'><hr style='border: 5px;' ></td></tr>
             <tr><td style='background:white;' colspan='9'>&nbsp;</td></tr>";
    }

    function actualizatipocobranza() {
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $idordenventa = $_REQUEST['idordenventa'];
        $data['idtipocobranza'] = $_REQUEST['idtipocobranza'];
        $exito = $ordenventa->actualizaOrdenVenta($data, $idordenventa);
        echo $exito;
    }

    /* Ordenes por Cliente */
    function listaOrdenesxIdCliente() {
        $id = $_REQUEST['id'];
        $ordenventa = new OrdenVenta();
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $data = $ordenventa->listaOrdenVentaxIdCliente($id);
        $nroOrdenes = count($data);
        if ($nroOrdenes > 0) {
            $montoTotal = 0;
            $montoPagado = 0;
            $montoDevuelto = 0;
            $saldo = 0;
            for ($i = 0; $i < $nroOrdenes; $i++) {
                $montoGuia = $ordenGasto->totalGuia($data[$i]['idordenventa']);
                $TipoCambio = $this->AutoLoadModel("TipoCambio");
                $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($data[$i]['IdTipoCambioVigente']);
                $simboloMoneda = $dataTipoCambio[0]['simbolo'];
                $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
                $acumulaxIdMoneda[$simboloMoneda]['Simbolo'] = $simboloMoneda;
                $acumulaxIdMoneda[$simboloMoneda]['MontoTotal'] += $montoGuia;
                $acumulaxIdMoneda[$simboloMoneda]['montoDevuelto'] += $data[$i]['importedevolucion'];
                $acumulaxIdMoneda[$simboloMoneda]['montoPagado'] += $data[$i]['importepagado'];
                $acumulaxIdMoneda[$simboloMoneda]['saldo'] += ($montoGuia - $data[$i]['importepagado']);
                echo "<tr>";
                echo    "<td>" . $data[$i]['codigov'] . "<input type='hidden' value='" . $data[$i]['codigov'] . "' class='codigov'></td>";
                echo    "<td>" . $data[$i]['fordenventa'] . "</td>";
                if ($data[$i]['desaprobado'] == 1) {
                    echo "<td>Desaprobado</td>";
                } else {
                    echo "<td>" . $data[$i]['fechadespacho'] . "</td>";
                }
                echo    "<td>" . $data[$i]['fechavencimiento'] . "</td>";
                echo    "<td>" . $data[$i]['vendedor'] . "</td>";
                echo    "<td>" . $simboloMoneda . "  " . number_format($montoGuia, 2) . "</td>";
                echo    "<td>" . $simboloMoneda . "  " . number_format($data[$i]['importepagado']/* -$data[$i]['importedevolucion'] */, 2) . "</td>";
                echo    "<td>" . $simboloMoneda . "  " . number_format($data[$i]['importedevolucion'], 2) . "</td>";
                echo    "<td>" . $simboloMoneda . "  " . number_format($montoGuia - $data[$i]['importepagado'], 2) . "</td>";
                echo    "<td>" . $data[$i]['situacion'] . "</td>";
                echo    "<td>" . $data[$i]['fechaCancelado'] . "</td>";
                echo    "<td><a href=\"/ordencobro/buscarDetalleOrdenCobro/" . $data[$i]['idordenventa'] . "\" class=\"btnVerDetalleOrden\">Ver Programacion</a><br><a href='/ordenventa/DetalleGuiaMadre/" . $data[$i]['idordenventa'] . "' class='btnDetalleProducto' >Ver Productos</a></td>";
                //echo "<td><a href=\"/ordencobro/detalleOrdenCobroVistaGlobal/".$data[$i]['idordenventa']."\" class=\"btnVerDetalleOrden\">Ver Detalle</a></td>";
                echo "</tr>";
            }
            echo "<tr><th colspan=4> </th><th>Acumulado en  S/.  :</th><th>" . number_format($acumulaxIdMoneda['S/']['MontoTotal'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['S/']['montoPagado'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['S/']['montoDevuelto'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['S/']['saldo'], 2) . "</th><th></th><th></th></tr>";       # code...
            echo "<tr><th colspan=4> </th><th>Acumulado en  US $ :</th><th>" . number_format($acumulaxIdMoneda['US $']['MontoTotal'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['US $']['montoPagado'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['US $']['montoDevuelto'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['US $']['saldo'], 2) . "</th><th></th><th></th></tr>";       # code...
        } else {
            echo "<tr>";
            echo    "<td colspan=\"11\">No se encontraron ordenes de venta</td>";
            echo "</tr>";
        }
    }

    function listaOrdenesDetallado() {
        $id = $_REQUEST['id'];
        $ordenventa = new OrdenVenta();
        $ordencobro = New OrdenCobro();
        $tipoGasto = $this->AutoLoadModel('tipogasto');
        $detalleOrdenCobro = New DetalleOrdenCobro();
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $data = $ordenventa->listaOrdenVentaxIdCliente($id);
        $nroOrdenes = count($data);
        if ($nroOrdenes > 0) {
            $montoTotal = 0;
            $montoPagado = 0;
            $montoDevuelto = 0;
            $saldo = 0;
            echo "<thead>
                    <tr><th colspan='12'>Mis Ordenes de Pedido</th></tr>
                    <tr>
                        <th>Nro Orden</th>
                        <th>Fecha<br>Pedido</th>
                        <th>Fecha<br>Despacho</th>
                        <th>Fecha<br>Vencimiento</th>
                        <th>Vendedor</th>
                        <th>Monto<br>Total</th>
                        <th>Monto<br>Pagado</th>
                        <th>Monto<br>Devuelto</th>	
                        <th>DEUDA</th>	
                        <th>Situación</th>
                        <th>Fecha Cancelado</th>
                        <th>&nbsp;</th>
                    </tr>
                  </thead>	
                <tbody>";
            for ($i = 0; $i < $nroOrdenes; $i++) {
                $montoGuia = $ordenGasto->totalGuia($data[$i]['idordenventa']);
                $TipoCambio = $this->AutoLoadModel("TipoCambio");
                $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($data[$i]['IdTipoCambioVigente']);
                $simboloMoneda = $dataTipoCambio[0]['simbolo'];
                $TC_PrecioVenta = $dataTipoCambio[0]['venta'];
                $acumulaxIdMoneda[$simboloMoneda]['Simbolo'] = $simboloMoneda;
                $acumulaxIdMoneda[$simboloMoneda]['MontoTotal'] += $montoGuia;
                $acumulaxIdMoneda[$simboloMoneda]['montoDevuelto'] += $data[$i]['importedevolucion'];
                $acumulaxIdMoneda[$simboloMoneda]['montoPagado'] += $data[$i]['importepagado'];
                $acumulaxIdMoneda[$simboloMoneda]['saldo'] += ($montoGuia - $data[$i]['importepagado']);
                echo "<tr class='filaContenedor' style='border:solid 1px red; background-color:rgb(124, 180, 224);'>";
                echo    "<th>" . $data[$i]['codigov'] . "<input type='hidden' value='" . $data[$i]['codigov'] . "' class='codigov'></th>";
                echo    "<th>" . $data[$i]['fordenventa'] . "</th>";
                if ($data[$i]['desaprobado'] == 1) {
                    echo "<th>Desaprobado</th>";
                } else {
                    echo "<th>" . $data[$i]['fechadespacho'] . "</th>";
                }

                echo    "<th>" . $data[$i]['fechavencimiento'] . "</th>";
                echo    "<th>" . $data[$i]['vendedor'] . "</th>";
                echo    "<th>" . $simboloMoneda . "  " . number_format($montoGuia, 2) . "</th>";
                echo    "<th>" . $simboloMoneda . "  " . number_format($data[$i]['importepagado'] - $data[$i]['importedevolucion'], 2) . "</th>";
                echo    "<th>" . $simboloMoneda . "  " . number_format($data[$i]['importedevolucion'], 2) . "</td>";
                echo    "<th>" . $simboloMoneda . "  " . number_format($montoGuia - $data[$i]['importepagado'], 2) . "</td>";
                echo    "<th>" . $data[$i]['situacion'] . "</td>";
                echo    "<th>" . $data[$i]['fechaCancelado'] . "</td>";
                echo    "<td><a href=\"/ordencobro/buscarDetalleOrdenCobro/" . $data[$i]['idordenventa'] . "\" class=\"btnVerDetalleOrden\">Ver Programacion</a><br><a href='/ordenventa/DetalleGuiaMadre/" . $data[$i]['idordenventa'] . "' class='btnDetalleProducto' >Ver Productos</a></td>";
                echo "</tr>";
                echo "<tr>";
                $dataOrdenCobro = $ordencobro->listarxguia($data[$i]['idordenventa']);
                $cantidadOrdenCobro = count($dataOrdenCobro);
                echo "<tr>";
                echo    "<td>Nro:" . $cantidadOrdenCobro . "</td>";
                echo    "<td colspan='2'>Monto:</td>";
                echo    "<td colspan='2'>Saldo:</td>";
                echo    "<td colspan='2'>Condicion:</td>";
                echo    "<td>Nro letra:</td>";
                echo    "<td>Fecha Vencimiento:</td>";
                echo    "<td>Situación:</td>";
                echo    "<td>Ref</td>";
                echo    "<td>Tipo Gasto:</td>";
                echo "</tr>";
                $var = 0;
                for ($y = 0; $y < $cantidadOrdenCobro; $y++) {
                    $dataDetalleOrdenCobro = $detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$y]['idordencobro']);
                    $tamanio = count($dataDetalleOrdenCobro);
                    for ($x = 0; $x < $tamanio; $x++) {
                        $var++;
//                   $dataActor=$actor->buscarxid($dataDetalleOrdenCobro[$i]['usuariomodificacion']);
                        if ($dataDetalleOrdenCobro[$x]['situacion'] != 'reprogramado') {
                            echo "<tr>";
                            echo    "<td style='text-align:center;'>" . ($var) . "</td>";
                            echo    "<td colspan='2'>" . $simboloMoneda . " " . number_format($dataDetalleOrdenCobro[$x]['importedoc'], 2) . "</td>";
                            echo    "<td colspan='2'>" . $simboloMoneda . " " . number_format($dataDetalleOrdenCobro[$x]['saldodoc'], 2) . "</td>";
                            switch ($dataDetalleOrdenCobro[$x]['formacobro']) {
                                case '1': $formacobro = "Contado";
                                    break;
                                case '2': $formacobro = "Crédito";
                                    break;
                                case '3': $formacobro = "Letras";
                                    break;
                            }
                            echo    "<td colspan='2'>" . $formacobro . "</td>";
                            echo    "<td>" . $dataDetalleOrdenCobro[$x]['numeroletra'] . "</td>";
                            echo    "<td>" . $dataDetalleOrdenCobro[$x]['fvencimiento'] . "</td>";
                            echo    "<td ><label>" . (($dataDetalleOrdenCobro[$x]['situacion'] == '') ? 'pendiente ref (' . $dataDetalleOrdenCobro[$x]['referencia'] . ')' : $dataDetalleOrdenCobro[$x]['situacion'] . ' ref (' . $dataDetalleOrdenCobro[$x]['referencia'] . ')') . "</label></td>";
                            echo    "<td>" . $dataDetalleOrdenCobro[$x]['recepcionLetras'] . "</td>";
                            echo    "<td>" . $tipoGasto->nombreGasto($dataDetalleOrdenCobro[$x]['tipogasto']) . "</td>";
                            echo "</tr>";
                        }
                    }
//            echo "<tr ><td colspan='12'>&nbsp</td></tr>";
                }
                echo "<tr ><td colspan='12'>&nbsp</td></tr>";
                echo    "</td>	
                     </tr>";
            }
            echo "<tr><th colspan=4> </th><th>Acumulado en  S/.  :</th><th>" . number_format($acumulaxIdMoneda['S/']['MontoTotal'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['S/']['montoPagado'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['S/']['montoDevuelto'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['S/']['saldo'], 2) . "</th><th></th><th></th><th></th></tr>";       # code...
            echo "<tr><th colspan=4> </th><th>Acumulado en  US $ :</th><th>" . number_format($acumulaxIdMoneda['US $']['MontoTotal'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['US $']['montoPagado'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['US $']['montoDevuelto'], 2) . "</th><th>" . number_format($acumulaxIdMoneda['US $']['saldo'], 2) . "</th><th></th><th></th><th></th></tr>";       # code...        
        } else {
            echo "<tr>";
            echo    "<td colspan=\"11\">No se encontraron ordenes de venta</td>";
            echo "</tr>";
        }
        //echo json_encode($data);
    }

    function OrdenesxIdCliente() {
        $ordenventa = $this->AutoLoadModel("ordenventa");
        $ingresos = $this->AutoLoadModel('ingresos');
        $ordencobro = $this->AutoLoadModel('ordencobro');
        $idcliente = $_REQUEST['idcliente'];
        $dataOrdenVenta = $ordenventa->buscarxidCliente($idcliente);
        $cantidad = count($dataOrdenVenta);
        $totalIngreso = 0;
        $totalSaldo = 0;
        $totalGuia = 0;
        $totalPagado = 0;
        $totalDeuda = 0;
        $resp .= "<div>";
        $resp .=    "<table>";
        $resp .=        "<tr>";
        $resp .=            "<th colspan='6'>Orden Venta</th>";
        $resp .=            "<th colspan='2'>Ingresos</th>";
        $resp .=            "<th colspan='1' rowspan='2'>Acciones </th>";
        $resp .=        "</tr>";
        $resp .=        "<tr>";
        $resp .=            "<th>Orden de Venta </th>";
        $resp .=            "<th>Situacion </th>";
        $resp .=            "<th>Monto Guia</th>";
        $resp .=            "<th>Monto Pagado</th>";
        $resp .=            "<th>Monto Devuelto</th>";
        $resp .=            "<th>Deuda Guia</th>";
        $resp .=            "<th>Ingresos</th>";
        $resp .=            "<th>Saldo Disponible </th>";
        $resp .=        "</tr>";

        for ($i = 0; $i < $cantidad; $i++) {
            $dataingreso = $ingresos->sumaIngresos($dataOrdenVenta[$i]['idordenventa']);
            $montoTotal = $ordencobro->deudatotal($dataOrdenVenta[$i]['idordenventa']);
            $resp .= "<tr>";
            $resp .=    "<td style='text-align:center;''><label class='lblorden'>" . $dataOrdenVenta[$i]['codigov'] . "</label> <input type='hidden' class='idordenventa' value='" . $dataOrdenVenta[$i]['idordenventa'] . "'> </td>";
            $resp .=    "<td>" . $dataOrdenVenta[$i]['situacion'] . "</td>";
            $resp .=    "<td style='text-align:right;'>S/." . number_format($montoTotal, 2) . "</td>";
            $resp .=    "<td style='text-align:right;'>S/." . number_format($dataOrdenVenta[$i]['importepagado'], 2) . "</td>";
            $resp .=    "<td style='text-align:right;'>S/." . number_format($dataOrdenVenta[$i]['importedevolucion'], 2) . "</td>";
            $resp .=    "<td style='text-align:right;'>S/." . number_format(($montoTotal - $dataOrdenVenta[$i]['importepagado']), 2) . "</td>";
            $resp .=    "<td style='text-align:right;'>S/." . number_format($dataingreso[0]['sum(montoasignado)'] + $dataingreso[0]['sum(saldo)'], 2) . "</td>";
            $resp .=    "<td style='text-align:right;'>S/." . number_format($dataingreso[0]['sum(saldo)'], 2) . "<input type='hidden' value='" . number_format($dataingreso[0]['sum(saldo)'], 2) . "'></td>";
            $totalIngreso += $dataingreso[0]['sum(montoingresado)'];
            $totalSaldo += $dataingreso[0]['sum(saldo)'];
            $totalGuia += $montoTotal;
            $totalPagado += $dataOrdenVenta[$i]['importepagado'];
            $totalDeuda += 0;
            $resp .=    "<td><a title='Ver Detalle de Ingresos' class='cabecera' id=" . $dataOrdenVenta[$i]['idordenventa'] . " href='#'><img style='display:block;margin:auto;' src='/imagenes/ver.gif'></a> </td>";
            $resp .= "</tr>";
        }
        $resp .=    "</table>";
        $resp .= "</div>";
        echo $resp;
    }

    function porcentajeComision() {
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $comision = $this->AutoLoadModel('comision');
        $idordenventa = $_REQUEST['idordenventa'];
        $porcentaje = $_REQUEST['porcentaje'];
        $data['porComision'] = $porcentaje;
        //$data['escomisionado']=1;
        $exito = $ordenventa->actualizaOrdenVenta($data, $idordenventa);
        $dataBusqueda = $comision->buscarxidOrdenVenta($idordenventa);
        if (!empty($dataBusqueda)) {
            $data2['idordenventa'] = $idordenventa;
            $data2['porcentajecom'] = $porcentaje;
            $exito2 = $comision->actualizaComision($data2, $idordenventa);
        } else {
            $data2['idordenventa'] = $idordenventa;
            $data2['porcentajecom'] = $porcentaje;
            $exito2 = $comision->grabar($data2);
        }
        echo $exito2;
    }

    function porcentajeComision2() {
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $comision = $this->AutoLoadModel('comision');
        $idordenventa = $_REQUEST['idordenventa'];
        $porcentaje = $_REQUEST['porcentaje'];
        $data['porComision'] = $porcentaje;
        //$data['escomisionado']=1;
        $exito = $ordenventa->actualizaOrdenVenta($data, $idordenventa);
        $importeov = 0;
        $importeganado = 0;
        $dataOV = $ordenventa->buscaOrdenVenta($idordenventa);
        if (count($dataOV) > 0) {
            $importeov = round(($dataOV[0]['importeov'] - $dataOV[0]['importedevolucion']) / 1.18, 2);
            $importeganado = round(($porcentaje * $importeov) / 100, 2);
        }
        $data2['importetotal'] = $importeov;
        $data2['importeganado'] = $importeganado;
        $dataBusqueda = $comision->buscarxidOrdenVenta($idordenventa);
        if (!empty($dataBusqueda)) {
            $data2['idordenventa'] = $idordenventa;
            $data2['porcentajecom'] = $porcentaje;
            $exito2 = $comision->actualizaComision($data2, $idordenventa);
        } else {
            $data2['idordenventa'] = $idordenventa;
            $data2['porcentajecom'] = $porcentaje;
            $exito2 = $comision->grabar($data2);
        }
        echo $exito2;
    }

    function comisionar() {
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $comision = $this->AutoLoadModel('comision');
        $idordenventa = $_REQUEST['idordenventa'];
        $fcomision = $_REQUEST['fechacomision'];
        if (!empty($fcomision)) {
            $fcomision = date('Y-m-d', strtotime($fcomision));
        }
        $data['escomisionado'] = 1;
        $data['fcomision'] = $fcomision;
        $exito = $ordenventa->actualizaOrdenVenta($data, $idordenventa);
        echo $exito;
    }

    function actualizaPago() {
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $monto = $_REQUEST['valorMonto'];
        $idordenventa = $_REQUEST['idordenventa'];
        $dataBusqueda = $ordenventa->buscarOrdenVentaxId($idordenventa);
        $importepagadoAnterior = $dataBusqueda[0]['importepagado'];
        //actualizamos el importepagado
        $data['importepagado'] = $importepagadoAnterior + $monto;
        $exito = $ordenventa->actualizaOrdenVenta($data, $idordenventa);
    }

    function descuentaPago() {
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $monto = $_REQUEST['valorMonto'];
        $idordenventa = $_REQUEST['idordenventa'];
        $dataBusqueda = $ordenventa->buscarOrdenVentaxId($idordenventa);
        $importepagadoAnterior = $dataBusqueda[0]['importepagado'];
        //actualizamos el importepagado
        $data['importepagado'] = $importepagadoAnterior - $monto;
        $exito = $ordenventa->actualizaOrdenVenta($data, $idordenventa);
    }

    function listaFechaComision() {
        $ordenventa = $this->AutoLoadModel("ordenventa");
        $idvendedor = $_REQUEST['idvendedor'];
        $dataBusqueda = $ordenventa->listaFechaComision($idvendedor);
        $cantidadBusqueda = count($dataBusqueda);
        $data = "<option value='0'>Seleccione Fecha Comision</option>";
        for ($i = 0; $i < $cantidadBusqueda; $i++) {
            $data .= "<option value='" . $dataBusqueda[$i]['fcomision'] . "'>" . $dataBusqueda[$i]['fcomision'] . "</option>";
        }
        echo $data;
    }

    function listaOrdenesNoRepitidas() {
        $idordenventa = $_REQUEST['idordenventa'];
        $idcliente = $_REQUEST['idcliente'];
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $dataOrdenVenta = $ordenventa->buscarxidClienteFiltro($idcliente, $idordenventa);
        $cantidad = count($dataOrdenVenta);
        $resp = "<option value=''>Seleccione Orden</option>";
        for ($i = 0; $i < $cantidad; $i++) {
            $resp .= "<option value='" . $dataOrdenVenta[$i]['idordenventa'] . "'>" . $dataOrdenVenta[$i]['codigov'] . "</option>";
        }
        echo $resp;
    }

    function grabaFechaDespacho() {
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $idordenventa = $_REQUEST['idordenventa'];
        if (!empty($_REQUEST['fechaDespacho'])) {
            $fechaDespacho = date('Y-m-d', strtotime($_REQUEST['fechaDespacho']));
            $data['fechadespacho'] = $fechaDespacho;
            $exito = $ordenventa->actualizaOrdenVenta($data, $idordenventa);
            if ($exito) {
                echo 'grabo';
            }
        }
    }

    function editarOrdenVenta() {
        $idordenventa = $_REQUEST['id'];
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $dataOrdenVenta = $ordenventa->buscarOrdenVentaxIdSinRestriccionAreas($idordenventa);
        if (!empty($_REQUEST['id']) && $_REQUEST['id'] > 0 && !empty($dataOrdenVenta)) {
            $descuento = $this->AutoLoadModel('descuento');
            $detalleordenventa = $this->AutoLoadModel('detalleordenventa');
            $clientezona = $this->AutoLoadModel('clientezona');
            $clienteTransporte = $this->AutoLoadModel('clientetransporte');
            $dataDetalleOrdenVenta = $detalleordenventa->listaDetalleProductos($idordenventa);
            $data['ordenventa'] = $dataOrdenVenta;
            if ($dataOrdenVenta[0]['vbventas'] != 1) {
                $data['detalleordenventa'] = $dataDetalleOrdenVenta;
                $data['descuento'] = $descuento->listado();
            }
            $data['redondeo'] = $this->configIni('Globals', 'Redondeo');
            $data['clientezona'] = $clientezona->buscaCliente($dataOrdenVenta[0]['idcliente']);
            $data['transporte'] = $clienteTransporte->buscaTransportexIdCliente($dataOrdenVenta[0]['idcliente']);
            $this->view->show('/ordenventa/editarordenventa.phtml', $data);
        } else {
            $ruta['ruta'] = "/facturacion/listaOrdenVenta";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function actualizaOrdenVenta() {
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $dataov['idcliente'] = $_REQUEST['idCliente'];
        $dataov['idclientezona'] = $_REQUEST['lstSucursal'];
        $dataov['direccion_envio'] = $_REQUEST['txtDireccionEnvio'];
        $dataov['direccion_despacho'] = $_REQUEST['txtDireccionDespacho'];
        $dataov['idclientetransporte'] = $_REQUEST['lstTransporte'];
        $dataov['idvendedor'] = $_REQUEST['idVendedor'];
        $dataov['contacto'] = $_REQUEST['txtContacto'];
        $ordenVenta = $this->AutoLoadModel('ordenventa');
        $ingresos = $this->AutoLoadModel('ingresos');
        $exito = $ordenVenta->actualizaOrdenVenta($dataov, $idOrdenVenta);
        if ($exito) {
            $dataIngreso['idcliente'] = $_REQUEST['idCliente'];
            $exito2 = $ingresos->actualiza($dataIngreso, "idordenventa='$idOrdenVenta'");
            if ($exito2) {
                $dataRespuesta['validacion'] = true;
            } else {
                $dataRespuesta['validacion'] = false;
            }
        } else {
            $dataRespuesta['validacion'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    function actualizaDetalleOrdenVenta() {
        $producto = $this->AutoLoadModel('producto');
        $ordenVenta = $this->AutoLoadModel('ordenventa');
        $detalleOrdenVenta = $this->AutoLoadModel('detalleordenventa');
        $dataDetalle = $_REQUEST['detalle'];
        $dataProducto = $_REQUEST['producto'];
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $idDetalleOV = $_REQUEST['detalleOV'];
        $totalImporte = $_REQUEST['importeov'];
        $cantidad = count($dataDetalle);
        for ($i = 0; $i < $cantidad; $i++) {
            $idProducto = $dataDetalle[$i]['idproducto'];
            $cantidadSolicitada = $dataDetalle[$i]['cantsolicitada'];
            $cantidadInicial = $dataProducto[$i]['cantidadInicial'];
            if ($idDetalleOV[$i]['iddetalleordenventa'] != 0) {
                $dataBusqueda = $producto->buscaProductoxId($idProducto);
                $stockDisponible = $dataBusqueda[0]['stockdisponible'];
                $idDetalleOrdenVenta = $idDetalleOV[$i]['iddetalleordenventa'];
                if ($dataDetalle[$i]['estado'] != 0) {
                    $dataP['stockdisponible'] = $stockDisponible + $cantidadInicial - $cantidadSolicitada;
                } else {
                    $dataP['stockdisponible'] = $stockDisponible + $cantidadInicial;
                }
                $exitoP = $producto->actualizaProducto($dataP, $idProducto);
                $exitoD = $detalleOrdenVenta->actualizaxFiltro($dataDetalle[$i], "iddetalleordenventa='$idDetalleOrdenVenta'");
            } else {
                if ($dataDetalle[$i]['estado'] != 0) {
                    $dataBusqueda = $producto->buscaProductoxId($idProducto);
                    $stockDisponible = $dataBusqueda[0]['stockdisponible'];
                    $dataP['stockdisponible'] = $stockDisponible - $cantidadSolicitada;
                    $dataDetalle[$i]['idordenventa'] = $idOrdenVenta;
                    $exitoP = $producto->actualizaProducto($dataP, $idProducto);
                    $exitoD = $detalleOrdenVenta->graba($dataDetalle[$i]);
                }
            }
        }
        if ($exitoD && $exitoP) {
            $dataOV['importeov'] = $totalImporte;
            $dataOV['importeaprobado'] = $totalImporte;
            $exitoO = $ordenVenta->actualizaOrdenVenta($dataOV, $idOrdenVenta);
            $ruta['ruta'] = "/ventas/autorizarventa";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function generafechavencimiento() {
        $ordencobro = $this->AutoLoadModel('ordencobro');
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $data = $ordenventa->listado();
        $cantidad = count($data);
        $cont = 0;
        for ($i = 0; $i < $cantidad; $i++) {
            $data2 = $ordencobro->buscafechavencimiento($data[$i]['idordenventa']);
            $data3['fechavencimiento'] = $data2[0]['fvencimiento'];

            $exito = $ordenventa->actualizaOrdenVenta($data3, $data[$i]['idordenventa']);
            if ($exito) {
                $cont++;
            }
        }
        echo "Se grabo : " . $cont . " de : " . $cantidad;
    }

    function cargaOrden() {
        $idordenventa = $_REQUEST['idordenventa'];
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $ordenIngresos = $this->AutoLoadModel('ingresos');
        $dataBusqueda = $ordenventa->buscarOrdenVentaxId($idordenventa);
        $fila = "<td>" . $dataBusqueda[0]['fechadespacho'] . "</td>";
        $fila .= "<td>" . $dataBusqueda[0]['fechavencimiento'] . "</td>";
        $fila .= "<td>" . $dataBusqueda[0]['fechaCancelado'] . "</td>";
        $importeGuia = $ordenGasto->totalGuia($idordenventa);
        $importepagado_ = $ordenIngresos->sumaIngresos($idordenventa);
        $importepagado = $importepagado_[0]['sum(montoasignado)'];
        $deuda = $importeGuia - $importepagado;
        $fila .= "<td>" . $dataBusqueda[0]['Simbolo'] . " " . number_format($importeGuia, 2) . "</td>";
        $fila .= "<td>" . $dataBusqueda[0]['Simbolo'] . " " . number_format($importepagado, 2) . "</td>";
        $fila .= "<td>" . $dataBusqueda[0]['Simbolo'] . " " . number_format($deuda, 2) . "</td>";
        $fila .= "<td>" . $dataBusqueda[0]['situacion'] . "</td>";
        echo $fila;
    }

    /**
      Inicio de Edicion de Orden Venta
     */
    function EdicionGlobalOrdenVenta() {
        $this->view->show("/ordenventa/edicionglobalordenventa.phtml");
    }

    function resetearOrdenVenta() {
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $OBJ_OrdenCobro = $this->AutoLoadModel("OrdenCobro");
        $dataOrdenCobro = $OBJ_OrdenCobro->buscaxordenventa($idOrdenVenta);
        $tam_dataOrdenCobro = count($dataOrdenCobro);
        for ($i = 0; $i < $tam_dataOrdenCobro; $i++) {
            $idOrdenCobro = $dataOrdenCobro[$i]['idordencobro'];
            $OBJ_OrdenCobro->eliminaOrdencobro($idOrdenCobro);
            $OBJ_DetalleOrdenCobro = $this->AutoLoadModel("DetalleOrdenCobro");
            $dataDetalleOrdenCobro = $OBJ_DetalleOrdenCobro->listadoxidOrdenCobro($idOrdenCobro);
            $tamDataDetalleOrdenCobro = count($dataDetalleOrdenCobro);
            for ($j = 0; $j < $tamDataDetalleOrdenCobro; $j++) {
                $idDetalleOrdenCobro = $dataDetalleOrdenCobro[$j]['iddetalleordencobro'];
                $OBJ_Ingresos = $this->AutoLoadModel("Ingresos");
                $dataIngresosOrdenVenta = $OBJ_Ingresos->IngresosxIdordenVenta($idOrdenVenta);
                $tam_dataIngresosOrdenVenta = count($dataIngresosOrdenVenta);
                for ($k = 0; $k < $tam_dataIngresosOrdenVenta; $k++) {
                    $idIngresosOrdenVenta = $dataIngresosOrdenVenta[$k]['idingresos'];
                    $OBJ_DetalleOrdenCobroIngresos = $this->AutoLoadModel("DetalleOrdenCobroIngreso");
                    $OBJ_DetalleOrdenCobroIngresos->InactivaxIdIngresosxIdDetalleOrdenCobro($idIngresosOrdenVenta, $idDetalleOrdenCobro);
                }
                $OBJ_Ingresos->liberaAsignacionxIdOrdenVenta($idOrdenVenta);
            }
            $exitoELiminaDetalleOrdenCobro = $OBJ_DetalleOrdenCobro->eliminaxIdOrdenCobro($idOrdenCobro);
        }
        $OBJ_OrdenCobro->eliminaxIdordenventa($idOrdenVenta);
        $OBJ_OrdenGasto = $this->AutoLoadModel("OrdenGasto");
        $OBJ_OrdenGasto->InactivaxIdOrdenventa($idOrdenVenta);
        $OBJ_Movimiento = $this->AutoLoadModel("Movimiento");
        $OBJ_Movimiento->InactivaMovimientoxIdOrdenVenta($idOrdenVenta);
        $dataOrdenVenta['vbventas'] = "-1";
        $dataOrdenVenta['vbcobranzas'] = "-1";
        $dataOrdenVenta['vbalmacen'] = "-1";
        $dataOrdenVenta['vbcreditos'] = "-1";
        $dataOrdenVenta['esguiado'] = 0;
        $dataOrdenVenta['esdespachado'] = 0;
        $dataOrdenVenta['fechadespacho'] = "0000-00-00";
        $dataOrdenVenta['fechavencimiento'] = "0000-00-00";
        $dataOrdenVenta['fechaCancelado'] = "0000-00-00";
        $dataOrdenVenta['nrocajas'] = 0;
        $dataOrdenVenta['nrobultos'] = 0;
        $dataOrdenVenta['esfacturado'] = 0;
        $dataOrdenVenta['guiaremision'] = 0;
        $dataOrdenVenta['importepagado'] = 0;
        $dataOrdenVenta['observaciones'] = '';
        $dataOrdenVenta['es_contado'] = 0;
        $dataOrdenVenta['es_credito'] = 0;
        $dataOrdenVenta['es_letras'] = 0;
        $dataOrdenVenta['tipo_letra'] = 0;
        $OBJ_ordenVenta = $this->AutoLoadModel("OrdenVenta");
        $exito = $OBJ_ordenVenta->actualizaOrdenVenta($dataOrdenVenta, $idOrdenVenta);
    }

    function cambiarmoneda() {
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $idMoneda = $_REQUEST['idMoneda'];
        $detalleOrdenVenta = $this->AutoLoadModel("DetalleOrdenVenta");
        $dataDetalleOrdenVenta = $detalleOrdenVenta->listaDetalleOrdenVentaGuia($idOrdenVenta);
        $tam = count($dataDetalleOrdenVenta);
        for ($i = 0; $i < $tam; $i++) {
            
        }
    }

    function autocompleteGuiasSinDocumento() {
        $texIni = $_REQUEST['term'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->buscaGuiasSinDocumento($texIni);
        echo json_encode($data);
    }

    function cargarDetallePercepcion() {
        $idOrdenVenta = $_REQUEST['id'];
        $documento = $this->AutoLoadModel("Documento");
        $detalles = $documento->getDetallePercepcion($idOrdenVenta);
        $tam = count($detalles);
        $tbl = "";
        $total = 0;
        $totalPercepcion = 0;
        $totalPercepcionAsignar = 0;
        $moneda = "";
        for ($i = 0; $i < $tam; $i++) {
            /* if ($detalles[$i]['porcentajefactura'] != "") {
              $detalles[$i]['montofacturado'] = (($detalles[$i]['montofacturado'] * $detalles[$i]['porcentajefactura']) / 100);
              } */
            $porcentaje = $detalles[$i]['percepcion'] * 100;
            $percepcion = $detalles[$i]['montofacturado'] * $detalles[$i]['percepcion'];
            if ($detalles[$i]['numdocp'] == 0 && ($detalles[$i]['esCargado'] == 0 || $detalles[$i]['esAnulado'] == 1)) {
                $tbl .= "<tr style='background: #fcc'>";
            } else {
                $tbl .= "<tr>";
            }
            $tbl .= "<td>" . str_pad($detalles[$i]['nombredoc'], 2, "0", STR_PAD_LEFT) . "</td>";
            $tbl .= "<td>F" . str_pad($detalles[$i]['serie'], 3, "0", STR_PAD_LEFT) . " - " . str_pad($detalles[$i]['numdoc'], 8, "0", STR_PAD_LEFT) . "</td>";
            $tbl .= "<td>" . $detalles[$i]['fechadoc'] . "</td>";
            $tbl .= "<td>" . $detalles[$i]['simbolo'] . " " . number_format($detalles[$i]['montofacturado'], 2) . "</td>";
            $tbl .= "<td>" . $porcentaje . "%</td>";
            $tbl .= "<td>" . $detalles[$i]['simbolo'] . " " . number_format($percepcion, 2) . "</td>";
            $tbl .= "<td>" . $detalles[$i]['simbolo'] . " " . number_format($detalles[$i]['montofacturado'] + $percepcion, 2) . "</td>";
            if ($detalles[$i]['numdocp'] > 0) {
                $tbl .= "<td><b>P" . str_pad($detalles[$i]['seriep'], 3, "0", STR_PAD_LEFT) . " - " . str_pad($detalles[$i]['numdocp'], 8, "0", STR_PAD_LEFT) . "</b></td>";
            } else {
                if ($detalles[$i]['esAnulado'] == 1) {
                    $tbl .= "<td><b class='red'>Anulado</b></td>";
                } else if ($detalles[$i]['esCargado'] == 1) {
                    $totalPercepcion += $percepcion;
                    $total = $total + $detalles[$i]['montofacturado'];
                    $tbl .= "<td><img src='/imagenes/impfebien.png'></td>";
                    if ($detalles[$i]['idRelacionado'] == 0) {
                        $totalPercepcionAsignar += $percepcion;
                    }
                } else {
                    $tbl .= "<td><img src='/imagenes/impfe.png'></td>";
                }
            }
            $tbl .= "</tr>";
            $moneda = $detalles[$i]['simbolo'] . " ";
        }
        $tblOrdenCobro = "";
        if ($totalPercepcionAsignar > 0) {
            $ordencobro = parent::AutoLoadModel('ordencobro');
            $dataOrdenCobro = $ordencobro->listarxguia($idOrdenVenta);
            $detalleOrdenCobro = parent::AutoLoadModel('detalleordencobro');
            $cantidadOrdenCobro = count($dataOrdenCobro);
            $redondeo = parent::configIni('Globals', 'Redondeo');
            $banderaOption = 0;
            for ($n = 0; $n < $cantidadOrdenCobro; $n++) {
                $dataDetalleOrdenCobro = $detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$n]['idordencobro']);
                $tamanio = count($dataDetalleOrdenCobro);
                $tblOrdenCobro .= "<table>";
                $tblOrdenCobro .=   "<tr>";
                $tblOrdenCobro .=       "<td colspan=11><h3>Condiciones financieras:</h3></td>";
                $tblOrdenCobro .=   "</tr>";
                $tblOrdenCobro .=   "<tr>";
                $tblOrdenCobro .=       "<th><img src='/imagenes/grabar.gif' width='20px'></th>";
                $tblOrdenCobro .=       "<th>Nro:</th>";
                $tblOrdenCobro .=       "<th>Padre</th>";
                $tblOrdenCobro .=       "<th>Monto:</th>";
                $tblOrdenCobro .=       "<th>Saldo:</th>";
                $tblOrdenCobro .=       "<th>Condicion:</th>";
                $tblOrdenCobro .=       "<th>Nro letra:</th>";
                $tblOrdenCobro .=       "<th>Fecha Giro:</th>";
                $tblOrdenCobro .=       "<th>Fecha Vencimiento:</th>";
                $tblOrdenCobro .=       "<th>Situación:</th>";
                $tblOrdenCobro .=       "<th>R. de letra :</th>";
                $tblOrdenCobro .=   "</tr>";
                for ($i = 0; $i < $tamanio; $i++) {
                    $tblOrdenCobro .= "<tr>";
                    if (strtolower($dataDetalleOrdenCobro[$i]['situacion']) == "" && $dataDetalleOrdenCobro[$i]['renovado'] == 0) {
                        $tblOrdenCobro .= "<td><center><input type='radio' name='txtIdDetalleOrdenCobro' value='" . $dataDetalleOrdenCobro[$i]['iddetalleordencobro'] . "' checked></center></td>";
                        $banderaOption = 1;
                    }
                    $tblOrdenCobro .= "<td>";
                    $tblOrdenCobro .= $dataDetalleOrdenCobro[$i]['iddetalleordencobro'];
                    $tblOrdenCobro .= "<input type='hidden' value='" . $dataDetalleOrdenCobro[$i]['iddetalleordencobro'] . "' class='idDetalleOrdenCobro'>";
                    $tblOrdenCobro .= "<input type='hidden' value='" . round($dataDetalleOrdenCobro[$i]['importedoc'], $redondeo) . "' class='importe'>";
                    $tblOrdenCobro .= "<input type='hidden' value='" . round($dataDetalleOrdenCobro[$i]['saldodoc'], $redondeo) . "' class='saldo'>";
                    $tblOrdenCobro .= "<input type='hidden' value='" . $dataDetalleOrdenCobro[$i]['numeroletra'] . "' class='numDoc'>";
                    $tblOrdenCobro .= "</td>";
                    $tblOrdenCobro .= "<td>" . $dataDetalleOrdenCobro[$i]['idpadre'] . "</td>";
                    $tblOrdenCobro .= "<td>" . ' ' . $moneda . number_format($dataDetalleOrdenCobro[$i]['importedoc'], $redondeo) . "</td>";
                    $tblOrdenCobro .= "<td>" . ' ' . $moneda . number_format($dataDetalleOrdenCobro[$i]['saldodoc'], $redondeo) . "</td>";
                    switch ($dataDetalleOrdenCobro[$i]['formacobro']) {
                        case '1': $formacobro = "Contado";
                            break;
                        case '2': $formacobro = "Crédito";
                            break;
                        case '3': $formacobro = "Letras";
                            break;
                    }
                    $tblOrdenCobro .= "<td>" . $formacobro . "</td>";
                    $tblOrdenCobro .= "<td>" . $dataDetalleOrdenCobro[$i]['numeroletra'] . "</td>";
                    $tblOrdenCobro .= "<td>" . $dataDetalleOrdenCobro[$i]['fechagiro'] . "</td>";
                    $tblOrdenCobro .= "<td>" . $dataDetalleOrdenCobro[$i]['fvencimiento'] . "</td>";
                    $situacion = empty($dataDetalleOrdenCobro[$i]['situacion']) ? "Pendiente" : $dataDetalleOrdenCobro[$i]['situacion'];
                    $tblOrdenCobro .= "<td>" . $situacion . " ref(" . $dataDetalleOrdenCobro[$i]['referencia'] . ")";
                    $tblOrdenCobro .= "<td>" . $dataDetalleOrdenCobro[$i]['recepcionLetras'] . "</td>";
                    $tblOrdenCobro .= "</tr>";
                }
                $tblOrdenCobro .=   "<tr>";
                $tblOrdenCobro .=       "<th colspan='2'>Monto Total:</th>";
                $tblOrdenCobro .=       "<td><b>" . ' ' . $moneda . number_format($dataOrdenCobro[$n]['importeordencobro'], $redondeo) . "</b></td>";
                $tblOrdenCobro .=       "<th colspan=8>&nbsp</th>";
                $tblOrdenCobro .=   "</tr>";
                $tblOrdenCobro .= "<table>";
            }
            $tblOrdenCobro .= "<table>";
            $tblOrdenCobro .=   "<tr>";
            $tblOrdenCobro .=       "<th><img src='/imagenes/grabar.gif' width='20px'></td>";
            $tblOrdenCobro .=       "<th colspan='9'></td>";
            $tblOrdenCobro .=   "</tr>";
            $tblOrdenCobro .=   "<tr>";
            $tblOrdenCobro .=       "<td style='width:4%'><center><input type='radio' name='txtIdDetalleOrdenCobro' value='-1'" . ($banderaOption == 0 ? " checked" : "") . "></center></td>";
            $tblOrdenCobro .=       "<td colspan='9'><b>Crear Orden de Cobro Independiente</b></td>";
            $tblOrdenCobro .=   "</tr>";
            $tblOrdenCobro .= "<table>";
        }
        $data['contenido'] = $tbl;
        $data['tblOrdenCobro'] = $tblOrdenCobro;
        $data['moneda'] = $moneda;
        $data['total'] = number_format($total, 2);
        $data['totalPercepcionAsignar'] = number_format($totalPercepcionAsignar, 2);
        $data['totalPercepcion'] = number_format($totalPercepcion, 2);
        echo json_encode($data);
    }

}

?>
