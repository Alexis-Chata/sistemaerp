<?php

class VendedorController extends ApplicationGeneral {

    function lista() {
        $vendedor = $this->AutoLoadModel('actor');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_vendedor'] = "";
        $data['vendedor'] = $vendedor->listaSoloVendedoresPaginado($_REQUEST['id']);
        $paginacion = $vendedor->paginadoSoloVendedores();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/vendedor/lista.phtml", $data);
    }

    function nuevo() {
        $departamento = new Departamento();
        $transporte = new Transporte();
        $datos['Departamento'] = $departamento->listado();
        $datos['TipoVendedor'] = $this->tipoCliente();
        $this->view->show("vendedor/nuevo.phtml", $datos);
    }

    function graba() {
        $vendedor = new Actor();
        $dataVendedor = $_REQUEST['Vendedor'];
        $dataVendedor['estado'] = 1;
        $dataVendedor['foto'] = $_FILES['foto']['name'];
        $id = $vendedor->grabaActor($dataVendedor);
        if ($id) {
            $codigo = $vendedor->GeneraCodigo();
            //$this->guardaImagenesFormularioGeneral($codigo['codigo'],'vendedor');
            $actorrol = new ActorRol();
            $datos['idactor'] = $id;
            $datos['idrol'] = 25;
            $datos['estado'] = 1;
            $id2 = $actorrol->grabaactorrol($datos);
            $ruta['ruta'] = "/vendedor/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function editar() {
        $id = $_REQUEST['id'];
        $vendedor = new Actor();
        $distrito = new Distrito();
        $provincia = new Provincia();
        $departamento = new Departamento();
        $dataVendedor = $vendedor->buscarxid($id);
        $dataVendedor['contrasena'] = $this->desencripta($dataVendedor['contrasena']);
        $data['Departamento'] = $departamento->listado();
        if ($dataVendedor[0]['iddistrito'] != '' && $dataVendedor[0]['iddistrito'] != 0) {
            $dataDistrito = $distrito->buscarxid($dataVendedor[0]['iddistrito']);
            $data['Provincia'] = $provincia->listado($dataDistrito[0]['codigodepto']);
            $data['Distrito'] = $distrito->listado($dataDistrito[0]['idprovincia']);
        }
        $data['RutaImagen'] = $this->rutaImagenesVendedor();
        $data['Vendedor'] = $dataVendedor;
        $this->view->show("vendedor/editar.phtml", $data);
    }

    function actualiza() {
        $vendedor = new Actor();
        $dataVendedor = $_REQUEST['Vendedor'];
        $dataVendedor['contrasena'] = $this->encripta($dataVendedor['contrasena']);
        $idvendedor = $_REQUEST['idvendedor'];
        if (count($_FILES)) {
            $dataVendedor['foto'] = $_FILES['foto']['name'];
        }
        $this->guardaImagenesFormularioGeneral($dataVendedor['codigo'], 'vendedor');
        $exito1 = $vendedor->ActualizaActor($dataVendedor, "idactor=" . $idvendedor);
        if ($exito1) {
            $ruta['ruta'] = "/vendedor/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function eliminar() {
        $id = $_REQUEST['id'];
        $vendedor = new Actor();
        $estado = $vendedor->EstadoActor($id);
        if ($estado) {
            $vendedor->EstadoActorRol($id);
            $ruta['ruta'] = "/vendedor/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function provincia() {
        $id = $_REQUEST['lstDepartamento'];
        $provincia = new Cliente();
        $data['Provincia'] = $provincia->listadoProvincia($id);
    }

    function Busqueda() {
        $data = New Cliente();
        $cliente = $data->buscaxRazonSocial($razonsocial);
        $datos['cliente'] = $cliente;
        $this->view->show("vendedor/lista.phtml", $datos);
    }

    function buscar() {
        $vendedor = $this->AutoLoadModel('actor');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_vendedor'];
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_vendedor'] = $_REQUEST['txtBusqueda'];
        }
        $parametro = $_SESSION['P_vendedor'];
        $paginacion = $vendedor->paginadoSoloVendedoresxnombre($parametro);
        $data['retorno'] = $parametro;
        $data['vendedor'] = $vendedor->listaSoloVendedoresPaginadoxnombre($_REQUEST['id'], $parametro);
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['totregistros'] = count($vendedor->SolobuscaxApellido($parametro));
        $this->view->show("/vendedor/buscar.phtml", $data);
        //$datos=$vendedor->listadoVendedoresTodos();
        //$objeto = $this->formatearparakui($datos);
        //header("Content-type: application/json");
        //echo "{\"data\":" .json_encode($objeto). "}";
        //echo json_encode($objeto);
    }

    function misordenes() {
        $ordenVenta = new OrdenVenta();
        $datos['ordenVenta'] = $ordenVenta->listarxvendedor($_SESSION['idactor']);
        $this->view->show('/vendedor/misordenes.phtml', $datos);
    }

    function autocompletevendedor() {
        $vendedor = new Actor();
        $text = $_REQUEST['term'];
        $datos = $vendedor->buscaautocompletev($text);//die(var_dump($datos));
        echo json_encode($datos);
    }

    function autocompletejefedelinea() {
        $vendedor = new Actor();
        $text = $_REQUEST['term'];
        $datos = $vendedor->buscaautocompletjefelinea($text);
        echo json_encode($datos);
    }

    function validarCodigo() {
        $vendedor = $this->AutoLoadModel('actor');
        $condicion = $_REQUEST['codigo'];
        $cantidad = $vendedor->verificaCodigo($condicion);
        if ($cantidad > 0) {
            $data['error'] = 'El codigo ya existe';
            $data['verificado'] = false;
            echo json_encode($data);
        } else {
            $data['error'] = 'Codigo Aceptado';
            $data['verificado'] = true;
            echo json_encode($data);
        }
    }

    function comisionVendedor() {
        $vededor = $this->AutoLoadModel("actor");
        $this->view->show('/vendedor/comisionVendedor.phtml', $data);
    }

    function comisionVendedor2() {
        $vededor = $this->AutoLoadModel("actor");
        $this->view->show('/vendedor/comisionVendedor2.phtml', $data);
    }

    function listaComisiones2() {
        $idvendedor = $_REQUEST['idvendedor'];
        $fechaInicial = $_REQUEST['fechaInicial'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        if (!empty($fechaInicial)) {
            $fechaInicial = date('Y-m-d', strtotime($fechaInicial));
        }
        if (!empty($fechaFinal)) {
            $fechaFinal = date('Y-m-d', strtotime($fechaFinal));
        }
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $documento = $this->AutoLoadModel('documento');
        $importeov = 0;
        $dataVendedor = $ordenventa->buscarOrdenComision($idvendedor, $fechaInicial, $fechaFinal);

        $cantidad = count($dataVendedor);
        $total = 0;
        $totalFacturas = 0;
        $totalSinFacturas = 0;
        for ($i = 0; $i < $cantidad; $i++) {
            $simbolomoneda = $dataVendedor[$i]['simbolo'];
            $importeov = round($dataVendedor[$i]['importeov'] / 1.18, 2);
            $importeDevolucion = round($dataVendedor[$i]['importedevolucion'] / 1.18, 2);
            $importe = $importeov - $importeDevolucion;
            $acumulaxIdMoneda[$simbolomoneda]['total'] += $importe;
            $acumulaxIdMoneda[$simbolomoneda]['comisionVendedor'] += $dataVendedor[$i]['porComision'] * $importe / 100;
            $acumulaxIdMoneda[$simbolomoneda]['totalSinFacturas'] += $importe;
            $fila .= "<tr>";
            $fila .=    "<td>" . $dataVendedor[$i]['codigov'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['fechadespacho'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['fechavencimiento'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['fechaCancelado'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['razonsocial'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['simbolo'] . " " . number_format($importeov, 2) . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['simbolo'] . " " . number_format($dataVendedor[$i]['importepagado'], 2) . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['simbolo'] . " " . number_format($importeDevolucion, 2) . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['simbolo'] . " " . number_format($importe, 2) . "</td>";
            //$fila .= "<td>" . number_format($dataDocumento[0]['porcentajefactura']) . "%</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['simbolo'] . " " . number_format(($dataVendedor[$i]['porComision'] * $importe) / 100, 2) . "</td>";
            $fila .=    "<td><input class='idordenventa' name='Comision[" . $i . "][idordenventa]' type='hidden' value=" . $dataVendedor[$i]['idordenventa'] . ">
                            <input type='text' name='Comision[" . $i . "][porComision]' value=" . $dataVendedor[$i]['porComision'] . " size='3' class='numeric txtcomision' ><a href='#' class='btnGrabar'><img width='25' higth='25' src='/imagenes/grabar.gif'></a></td>";
            $fila .= "</tr>";
        }
        $fila .= "<tr>
                    <td colspan='7'></td>
                    <th  style='text-align:right;'>Total</th> <td>S/. " . number_format($acumulaxIdMoneda['S/']['total'], 2) . "</td>
                    <th>Total<br>Comision</th><td>S/. " . number_format($acumulaxIdMoneda['S/']['comisionVendedor'], 2) . "</td>
                 </tr>";
        $fila .= "<tr>
                    <td colspan='7'></td>
                    <th style='text-align:right;'>Total</th> <td>US $  " . number_format($acumulaxIdMoneda['US $']['total'], 2) . "</td>
                    <th>Total<br>Comision</th><td>US $  " . number_format($acumulaxIdMoneda['US $']['comisionVendedor'], 2) . "</td>
		 </tr>";
        echo $fila;
    }

    function listaComisiones() {
        $idvendedor = $_REQUEST['idvendedor'];
        $fechaInicial = $_REQUEST['fechaInicial'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        if (!empty($fechaInicial)) {
            $fechaInicial = date('Y-m-d', strtotime($fechaInicial));
        }
        if (!empty($fechaFinal)) {
            $fechaFinal = date('Y-m-d', strtotime($fechaFinal));
        }
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $documento = $this->AutoLoadModel('documento');
        $importeov = 0;
        $dataVendedor = $ordenventa->buscarOrdenComision($idvendedor, $fechaInicial, $fechaFinal);
        $cantidad = count($dataVendedor);
        $total = 0;
        $totalFacturas = 0;
        $totalSinFacturas = 0;
        for ($i = 0; $i < $cantidad; $i++) {
            $dataDocumento = $documento->buscadocumentoxordenventaPrimero($dataVendedor[$i]['idordenventa'], " nombredoc=1");
            $simbolomoneda = $dataVendedor[$i]['simbolo'];
            $importeov = $dataVendedor[$i]['importeov'];
            $importeDevolucion = $dataVendedor[$i]['importedevolucion'];
            $importe = $importeov - $importeDevolucion;
            $acumulaxIdMoneda[$simbolomoneda]['total'] += $importe;
            //$total+=$importe;
            //$comisionVendedor+=$dataVendedor[$i]['porComision']*$importe/100;
            $acumulaxIdMoneda[$simbolomoneda]['comisionVendedor'] += $dataVendedor[$i]['porComision'] * $importe / 100;
            if (!empty($dataDocumento[0]['porcentajefactura'])) {
                //$totalFacturas+=$importe;
                $acumulaxIdMoneda[$simbolomoneda]['totalFacturas'] += $importe;
            } else {
                //$totalSinFacturas+=$importe;
                $acumulaxIdMoneda[$simbolomoneda]['totalSinFacturas'] += $importe;
            }
            $fila .= "<tr>";
            $fila .=    "<td>" . $dataVendedor[$i]['codigov'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['fechadespacho'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['fechavencimiento'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['fechaCancelado'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['razonsocial'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['simbolo'] . " " . number_format($importeov, 2) . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['simbolo'] . " " . number_format($dataVendedor[$i]['importepagado'], 2) . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['simbolo'] . " " . number_format($importeDevolucion, 2) . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['simbolo'] . " " . number_format($importe, 2) . "</td>";
            $fila .=    "<td>" . number_format($dataDocumento[0]['porcentajefactura']) . "%</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['simbolo'] . " " . number_format(($dataVendedor[$i]['porComision'] * $importe) / 100, 2) . "</td>";
            $fila .=    "<td><input class='idordenventa' name='Comision[" . $i . "][idordenventa]' type='hidden' value=" . $dataVendedor[$i]['idordenventa'] . ">
			   <input type='text' name='Comision[" . $i . "][porComision]' value=" . $dataVendedor[$i]['porComision'] . " size='3' class='numeric txtcomision' ><a href='#' class='btnGrabar'><img width='25' higth='25' src='/imagenes/grabar.gif'></a></td>";
            $fila .= "</tr>";
        }
        $fila .= "<tr>
                    <th>Total <br> C/F</th><td>S/. " . number_format($acumulaxIdMoneda['S/']['totalFacturas'], 2) . "</td>
                    <th>Total <br> S/F</th><td>S/. " . number_format($acumulaxIdMoneda['S/']['totalSinFacturas'], 2) . "</td>
                    <td colspan='3'></td>
                    <th  style='text-align:right;'>Total</th> <td>S/. " . number_format($acumulaxIdMoneda['S/']['total'], 2) . "</td>
                    <th>Total<br>Comision</th><td>S/. " . number_format($acumulaxIdMoneda['S/']['comisionVendedor'], 2) . "</td>
		 </tr>";
        $fila .= "<tr>
                    <th>Total <br> C/F</th><td>US $ " . number_format($acumulaxIdMoneda['US $']['totalFacturas'], 2) . "</td>
                    <th>Total <br> S/F</th><td>US $  " . number_format($acumulaxIdMoneda['US $']['totalSinFacturas'], 2) . "</td>
                    <td colspan='3'></td>
                    <th  style='text-align:right;'>Total</th> <td>US $  " . number_format($acumulaxIdMoneda['US $']['total'], 2) . "</td>
                    <th>Total<br>Comision</th><td>US $  " . number_format($acumulaxIdMoneda['US $']['comisionVendedor'], 2) . "</td>
                 </tr>";
        echo $fila;
    }

    function listaComisionesPagadas() {
        $idvendedor = $_REQUEST['idvendedor'];
        $fechacomision = $_REQUEST['fechacomision'];
        if (!empty($fechacomision)) {
            $fechacomision = date('Y-m-d', strtotime($fechacomision));
        }
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $documento = $this->AutoLoadModel('documento');
        $importeov = 0;
        $dataVendedor = $ordenventa->buscarOrdenComisionPagada($idvendedor, $fechacomision, "", "");
        $cantidad = count($dataVendedor);
        $total = 0;
        $totalFacturas = 0;
        $totalSinFacturas = 0;
        for ($i = 0; $i < $cantidad; $i++) {
            $dataDocumento = $documento->buscadocumentoxordenventaPrimero($dataVendedor[$i]['idordenventa'], " nombredoc=1");
            $importeov = $dataVendedor[$i]['importeov'];
            $importeDevolucion = $dataVendedor[$i]['importedevolucion'];
            $importe = $importeov - $importeDevolucion;
            $total += $importe;
            $comisionVendedor += $dataVendedor[$i]['porComision'] * $importe / 100;
            if (!empty($dataDocumento[0]['porcentajefactura'])) {
                $totalFacturas += $importe;
            } else {
                $totalSinFacturas += $importe;
            }
            $fila .= "<tr>";
            $fila .=    "<td>" . $dataVendedor[$i]['codigov'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['fechadespacho'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['fechavencimiento'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['fechaCancelado'] . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['razonsocial'] . "</td>";
            $fila .=    "<td>S/. " . number_format($importeov, 2) . "</td>";
            $fila .=    "<td>S/. " . number_format($dataVendedor[$i]['importepagado'], 2) . "</td>";
            $fila .=    "<td>S/. " . number_format($importeDevolucion, 2) . "</td>";
            $fila .=    "<td>S/. " . number_format($importe, 2) . "</td>";
            $fila .=    "<td>" . number_format($dataDocumento[0]['porcentajefactura']) . "%</td>";
            $fila .=    "<td>S/. " . number_format(($dataVendedor[$i]['porComision'] * $importe) / 100, 2) . "</td>";
            $fila .=    "<td>" . $dataVendedor[$i]['porComision'] . " </td>";
            $fila .= "</tr>";
        }
        $fila .= "<tr>
                    <th>Total con<br> Facturas</th><td>S/. " . number_format($totalFacturas, 2) . "</td>
                    <th>Total sin<br> Facturas</th><td>S/. " . number_format($totalSinFacturas, 2) . "</td>
                    <td colspan='3'></td>
                    <th  style='text-align:right;'>Total</th> <td>S/. " . number_format($total, 2) . "</td>
                    <th>Total<br>Comision</th><td>S/. " . number_format($comisionVendedor, 2) . "</td><td>&nbsp</td>
		 </tr>";
        echo $fila;
    }

    function generaCodigotodos() {
        //genera todos lo codigos de los actores
        $actor = $this->AutoLoadModel('actor');
        $dataActor = $actor->listaTodosActores();
        $cantidad = count($dataActor);
        $cont = 0;
        $cont2 = 0;
        for ($i = 0; $i < $cantidad; $i++) {
            $idactor = $dataActor[$i]['idactor'];
            $filtro = "idactor='$idactor'";
            $data['codigo'] = 'GC' . str_pad($idactor, 5, '0', STR_PAD_LEFT);
            $exito = $actor->ActualizaActor($data, $filtro);
            if ($exito) {
                $cont++;
            } else {
                $cont2++;
            }
        }
        echo 'Cantidad Registros correctos :' . $cont;
        echo 'Cantidad Errores : ' . $cont2;
    }

    function comisiondeventas() {
        $this->view->show('/vendedor/comisiondeventas.phtml');
    }

}

?>