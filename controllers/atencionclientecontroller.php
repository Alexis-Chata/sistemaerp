<?php

class AtencionclienteController extends ApplicationGeneral {

    function recepcionmercaderia() {
        $id = $_REQUEST['id'];
        if (!empty($id)) {
            $atcliente = new Atencioncliente();
            $cliente = new Cliente();
            $dataRecepcion = $atcliente->verRecepcionXid($id, " and aprobado=0");
            if (count($dataRecepcion) > 0) {
                $dataCliente = $cliente->verClienteAtencionCliente($dataRecepcion[0]['idcliente']);
                $dataDetalles = $atcliente->listaDetalleRecepcion($dataRecepcion[0]['idrecepcion']);
                $tam = count($dataDetalles);
                $acumuladorDetalle = "";
                for ($i = 0; $i < $tam; $i++) {
                    $dataProducto = $atcliente->productoxDetalleordenventa($dataDetalles[$i]['iddetalleordenventa'], $dataRecepcion[0]['idcliente']);
                    $acumuladorDetalle .= "<tr>" .
                                            '<td>' . $dataProducto[0]['nombrevendedor'] . '</td>' .
                                            "<td>" .
                                            '<input type="hidden" name="DRIddetalleordenventa[]" value="' . $dataDetalles[$i]['iddetalleordenventa'] . '">' .
                                            '<input type="text" id="Cant' . $dataDetalles[$i]['iddetalleordenventa'] . '" name="DRCantidad[]" value="' . $dataDetalles[$i]['cantidad'] . '" size="5" readonly>' .
                                            "</td>" .
                                            "<td>" . $dataProducto[0]['codigopa'] . "</td>" .
                                            "<td>" . $dataProducto[0]['nompro'] . "</td>" .
                                            "<td>" . $dataProducto[0]['codigov'] . "</td>" .
                                            "<td style='text-align: center'>"
                                                . "<input type='hidden' value='" . $dataDetalles[$i]['garantia'] . "' name='DRGarantia[]' id='Garantia" . $dataDetalles[$i]['iddetalleordenventa'] . "'>"
                                                . "<input type='checkbox' class='chkGarantia' data-id='" . $dataDetalles[$i]['iddetalleordenventa'] . "'" . ($dataDetalles[$i]['garantia'] == 1 ? ' checked' : '') . ">"
                                            . "</td>" .
                                            "<td><textarea class='text-300' name='DRObservaciones[]'>" . $dataDetalles[$i]['observaciones'] . "</textarea></td>" .
                                            '<td style="text-align: center">'
                                            . '<img src="/imagenes/error.jpg" class="eliminarDRM">'
                                            . '</td>' .
                            "</tr>";
                }
                $transporte = new Transporte();
                $data['dataTransporte'] = $transporte->buscarxId($dataRecepcion[0]['idagencia']);
            }
            $data['dataCliente'] = $dataCliente;
            $data['dataRecepcion'] = $dataRecepcion;
            $data['dataDetalle'] = $acumuladorDetalle;
        }
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['Responsables'] = $archivoConfig['Responsable'];
        $actorrol = new actorrol();
        $data['Recogedor']=$actorrol->actoresxRol(7);
        $this->view->show("/atencioncliente/recepcionmercaderia.phtml", $data);
    }

    function autocompleteproductoxovxcliente() {
        $term = $_REQUEST['term'];
        $idcliente = $_REQUEST['cliente'];
        $atcliente = new Atencioncliente();
        $data = $atcliente->autocompleteproductoxcliente($term, $idcliente);
        echo json_encode($data);
    }
    
    function autocompletecodigost() {
        $term = $_REQUEST['term'];
        $atencioncliente = new Atencioncliente();
        $data = $atencioncliente->buscarAutocompletecxodigost($term);
        echo json_encode($data);
    }

    function autocompleteagencia() {
        $term = $_REQUEST['term'];
        $transp = new Transporte();
        $data = $transp->buscarAutocomplete($term);
        echo json_encode($data);
    }

    function anadirproductoxcliente() {
        $iddetalleordenventa = $_REQUEST['iddetalleordenventa'];
        $idcliente = $_REQUEST['idcliente'];
        $txtcantidad = $_REQUEST['cantidad'];
        $atcliente = new Atencioncliente();
        $dataProducto = $atcliente->productoxDetalleordenventa($iddetalleordenventa, $idcliente);
        echo "<tr>" .
                '<td>' . $dataProducto[0]['nombrevendedor'] . '</td>' .
                "<td>" .
                '<input type="hidden" name="DRIddetalleordenventa[]" value="' . $dataProducto[0]['iddetalleordenventa'] . '">' .
                '<input type="text" id="Cant' . $dataProducto[0]['iddetalleordenventa'] . '" name="DRCantidad[]" value="' . $txtcantidad . '" size="5" readonly>' .
                "</td>" .
                "<td>" . $dataProducto[0]['codigopa'] . "</td>" .
                "<td>" . $dataProducto[0]['nompro'] . "</td>" .
                "<td>" . $dataProducto[0]['codigov'] . "</td>" .
                "<td style='text-align: center'>" . 
                    "<input type='hidden' value='0' name='DRGarantia[]' id='Garantia" . $dataProducto[0]['iddetalleordenventa'] . "'>"
                    . "<input type='checkbox' class='chkGarantia' data-id='" . $dataProducto[0]['iddetalleordenventa'] . "'>" . 
                "</td>" . 
                "<td><textarea class='text-300' name='DRObservaciones[]'></textarea></td>" .                 
                '<td style="text-align: center">'
                    . '<img src="/imagenes/error.jpg" class="eliminarDRM">' .
                '</td>' .
            "</tr>";
    }

    function productosxcliente() {
        $idproducto = $_REQUEST['idproducto'];
        $idcliente = $_REQUEST['idcliente'];
        $atcliente = new Atencioncliente();
        $datos = $atcliente->listaproductoxcliente($idproducto, $idcliente);
        $tam = count($datos);
        for ($i = 0; $i < $tam; $i++) {
            $simbolo = "S/ ";
            if ($datos[$i]['idmoneda'] == 2)
                $simbolo = "US $ ";
            echo '<tr id="rbp' . $datos[$i]['iddetalleordenventa'] . '">' .
            '<td>' . $datos[$i]['nombrevendedor'] . '</td>' .
            '<td><b>' . $datos[$i]['codigov'] . '</b></td>' .
            '<td>' . $datos[$i]['fordenventa'] . '</td>' .
            '<td>' . $datos[$i]['codigopa'] . '</td>' .
            '<td>' . $datos[$i]['nompro'] . '</td>' .
            '<td>' . $simbolo . number_format($datos[$i]['preciofinal'], 2) . '</td>' .
            '<td>' . $datos[$i]['cantidad'] . '</td>' .
            '<td>'
            . '<input type="text" class="txtAnadirProducto" data-dov="' . $datos[$i]['iddetalleordenventa'] . '" data-cantidad="' . $datos[$i]['cantidad'] . '" placeholder="' . $datos[$i]['cantidad'] . '" size="5">'
            . '<img src="/imagenes/+.jpg" width="25px" class="AnadirProducto" data-dov="' . $datos[$i]['iddetalleordenventa'] . '">'
            . '</td>' .
            '</tr>';
        }
    }

    function grabarecepcion() {
        $txtIdRrecepcion = $_REQUEST['txtIdRrecepcion'];
        $dataRecepcion = $_REQUEST['Recepcion'];
        $dataDRIddetalleordenventa = $_REQUEST['DRIddetalleordenventa'];
        $dataDRCantidad = $_REQUEST['DRCantidad'];
        $dataDRGarantia = $_REQUEST['DRGarantia'];
        $dataDRObservaciones = $_REQUEST['DRObservaciones'];
        $tam = count($dataDRIddetalleordenventa);        
        if ($tam > 0) {
            $atcliente = new Atencioncliente();
            if (!empty($txtIdRrecepcion)) {
                $bandRecepcion = $atcliente->verRecepcionXid($txtIdRrecepcion, " and aprobado=0");
                if (count($bandRecepcion) > 0) {
                    $atcliente->actualizaRecepcion($dataRecepcion, $txtIdRrecepcion);
                    $DetRecepcion['idrecepcion'] = $txtIdRrecepcion;
                    $atcliente->EliminaraDetallesRecepcion($txtIdRrecepcion);
                } else {
                    $tam = -1;
                }
            } else {
                $txtIdRrecepcion = 0;
                $DetRecepcion['idrecepcion'] = $atcliente->grabaRecepcion($dataRecepcion);
            }
            $DetRecepcion['estado'] = 1;
            for ($i = 0; $i < $tam; $i++) {
                $DetRecepcion['iddetalleordenventa'] = $dataDRIddetalleordenventa[$i];
                $DetRecepcion['cantidad'] = $dataDRCantidad[$i];
                $DetRecepcion['garantia'] = $dataDRGarantia[$i];
                $DetRecepcion['observaciones'] = $dataDRObservaciones[$i];                
                if ($txtIdRrecepcion == 0) {
                    $atcliente->grabaDetalleRecepcion($DetRecepcion);
                } else {
                    $dxDetRecep = $atcliente->buscaDetalleRecepcion($txtIdRrecepcion, $dataDRIddetalleordenventa[$i]);
                    if (count($dxDetRecep) > 0) {
                        $atcliente->actualizaDetalleRecepcion($DetRecepcion, $dxDetRecep[0]['iddetallerecepcion']);
                    } else {
                        $atcliente->grabaDetalleRecepcion($DetRecepcion);
                    }
                }
            }
        }
        $ruta['ruta'] = "/atencioncliente/recepcionmercaderia";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function listadomercaderia() {
        $atcliente = new Atencioncliente();
        $data['listaRecepcion'] = $atcliente->listaRecepcion(" and recepcion.aprobado=0");
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['prioridad'] = $archivoConfig['Prioridad'];
        $this->view->show("/atencioncliente/listadomercaderia.phtml", $data);
    }

    function verrecojomercaderia() {
        $id = $_REQUEST['idrecepcion'];
        $atcliente = new Atencioncliente();
        $cliente = new Cliente();
        $dataRecepcion = $atcliente->verRecepcionXid($id, " and recepcion.aprobado=0");
        $acumuladorDetalle = "";
        $acumuladorCabecera = "";
        if (count($dataRecepcion) > 0) {
            $dataCliente = $cliente->verClienteAtencionCliente($dataRecepcion[0]['idcliente']);
            $dataDetalles = $atcliente->listaDetalleRecepcion($dataRecepcion[0]['idrecepcion']);
            $tam = count($dataDetalles);
            $acumuladorCabecera = '<tr>' .                    
                                        '<th>Fecha y Hora de Impresion: </th>' .
                                        '<td>' . date('Y-m-d h-i-s') . '</td>' .
                                        '<th>Situacion: </th>' .
                                        '<td>Recogido</td>' .
                                        '<th>Documento: </th>' .
                                        '<td id="classNumero">N° ' . $dataRecepcion[0]['numero'] . '</td>' .
                                    '</tr>' .
                                    '<tr>' .
                                        '<th>Motivo: </th>' .
                                        '<td>' . $dataRecepcion[0]['nombremotivo'] . '</td>' .
                                        '<th>Observaciones: </th>' .
                                        '<td colspan="3">' . $dataRecepcion[0]['observaciones'] . '</td>' .
                                    '</tr>' .
                                    '<tr>' .
                                        '<th>Razon Social: </th>' .
                                        '<td>' . $dataCliente[0]['razonsocial'] . '</td>' .
                                        '<th>RUC: </th>' .
                                        '<td>' . $dataCliente[0]['rucdni'] . '</td>' .
                                        '<th>Fecha de Recojo: </th>' .
                                        '<td>' . $dataRecepcion[0]['fregistro'] . '</td>' .
                                    '</tr>' .
                                    '<tr>' .
                                        '<th>Direccion: </th>' .
                                        '<td colspan="3">' . $dataCliente[0]['direccion'] . ' - ' . $dataCliente[0]['ubigeo'] . ' | <i>' . $dataCliente[0]['zonacategoria'] . '</i></td>' .
                                        '<th>Celular: </th>' .
                                        '<td>' . $dataCliente[0]['celular'] . '</td>' .
                                    '</tr>';
            if ($dataRecepcion[0]['idagencia'] > 0) {
                $transporte = new Transporte();
                $dataTransporte = $transporte->buscarxId($dataRecepcion[0]['idagencia']); 
                if (count($dataTransporte) > 0) {
                    $archivoConfig = parse_ini_file("config.ini", true);
                    $Responsable = $archivoConfig['Responsable'];
                    $acumuladorCabecera .= '<tr>' .                    
                                                '<th>Razon Social Agencia: </th>' .
                                                '<td>' . $dataTransporte[0]['trazonsocial'] . '</td>' .
                                                '<th>Direccion Agencia: </th>' .
                                                '<td>' . $dataTransporte[0]['tdireccion'] . '</td>' .
                                                '<th>Guia de Remsion: </th>' .
                                                '<td>' . $dataRecepcion[0]['serie'] . ' - ' . $dataRecepcion[0]['correlativo'] . '</td>' .
                                            '</tr>' . 
                                            '<tr>' .  
                                                '<th>Costo de Envio: </th>' .
                                                '<td>S/ ' . number_format($dataRecepcion[0]['importe'], 2) . '</td>' .  
                                                '<th>Nota: </th>' .
                                                '<td colspan="3">El ' . $dataRecepcion[0]['porcentaje'] . '% del costo de envio es asumido por <b>La Empresa</b>' . ($dataRecepcion[0]['porcentaje'] != 100 && !empty($Responsable[$dataRecepcion[0]['responsable']]) ? ', la diferencia sera asumido por <b>'. $Responsable[$dataRecepcion[0]['responsable']] . '</b>' : '') . '.</td>' .
                                            '</tr>';
                }
            }            
            for ($i = 0; $i < $tam; $i++) {
                $dataProducto = $atcliente->productoxDetalleordenventa($dataDetalles[$i]['iddetalleordenventa'], $dataRecepcion[0]['idcliente']);
                $simbolo = "S/ ";
                if ($dataProducto[$i]['idmoneda'] == 2) $simbolo = "US $ ";
                $acumuladorDetalle .= "<tr>" .
                                        '<td>' . $dataProducto[0]['nombrevendedor'] . '</td>' .
                                        "<td>" . $dataProducto[0]['codigov'] . "</td>" . 
                                        "<td>" . $dataProducto[0]['fordenventa'] . "</td>" .
                                        "<td>" . $dataProducto[0]['codigopa'] . "</td>" .
                                        "<td>" . $dataProducto[0]['nompro'] . "</td>" .
                                        "<td style='text-align: right;'>" . $simbolo . number_format($dataProducto[0]['preciofinal'], 2) . "</td>" .
                                        "<td style='text-align: right;'>" . $dataDetalles[$i]['cantidad'] . "</td>" .
                                        "<td style='text-align: center;'>" . ($dataDetalles[$i]['garantia'] == 1 ? "<img src='/imagenes/correcto.png'>" : "") . "</td>" .
                                        "<td>" . $dataDetalles[$i]['observaciones'] . "</td>" .
                                    "</tr>";
            }            
        }
        $resp['cabecera'] = $acumuladorCabecera;
        $resp['detalle'] = $acumuladorDetalle;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function eliminarrecepcion() {
        $id = $_REQUEST['id'];
        $atcliente = new Atencioncliente();
        $data['estado'] = 0;
        $atcliente->actualizaRecepcion($data, $id, " and aprobado=0");
        $ruta['ruta'] = "/atencioncliente/listadomercaderia";
        $this->view->show("ruteador.phtml", $ruta);
    }
    
    function remitirrecepcion() {
        $idRecepcion = $_REQUEST['txtRecepRemision'];
        $dataRecepcion = $_REQUEST['Recepcion'];
        $dataRecepcion['aprobado'] = 1;
        $atcliente = new Atencioncliente();
        $dataRecepcion['codigost'] = $atcliente->generaCodigoST();
        $atcliente->actualizaRecepcion($dataRecepcion, $idRecepcion, " and aprobado=0");
        $ruta['ruta'] = "/atencioncliente/listadomercaderia";
        $this->view->show("ruteador.phtml", $ruta);
    }
    
    function gastoseingresos() {
        $this->view->show("/atencioncliente/gastoseingresos.phtml", $data);
    }
        
    function verrecojomercaderiaaprobado() {
        $id = $_REQUEST['idrecepcion'];
        $atcliente = new Atencioncliente();
        $cliente = new Cliente();
        $dataRecepcion = $atcliente->verRecepcionXid($id, " and recepcion.aprobado=1");
        $acumuladorDetalle = "";
        $acumuladorCabecera = "";
        $codigoST = "";
        if (count($dataRecepcion) > 0) {
            $codigoST = $dataRecepcion[0]['codigost'];
            $dataCliente = $cliente->verClienteAtencionCliente($dataRecepcion[0]['idcliente']);
            $dataDetalles = $atcliente->listaDetalleRecepcion($dataRecepcion[0]['idrecepcion']);
            $tam = count($dataDetalles);
            $acumuladorCabecera = '<tr>' .                    
                                        '<th>Fecha y Hora de Impresion: </th>' .
                                        '<td>' . date('Y-m-d h-i-s') . '</td>' .
                                        '<th>Situacion: </th>' .
                                        '<td>Aprobado</td>' .
                                        '<th>Documento: </th>' .
                                        '<td id="classNumero">N° ' . $dataRecepcion[0]['numero'] . '</td>' .
                                    '</tr>' .
                                    '<tr>' .
                                        '<th>Motivo: </th>' .
                                        '<td>' . $dataRecepcion[0]['nombremotivo'] . '</td>' .
                                        '<th>Observaciones: </th>' .
                                        '<td colspan="3">' . $dataRecepcion[0]['observaciones'] . '</td>' .
                                    '</tr>' .
                                    '<tr>' .
                                        '<th>Razon Social: </th>' .
                                        '<td colspan="2">' . $dataCliente[0]['razonsocial'] . '</td>' .
                                        '<th>RUC: </th>' .
                                        '<td colspan="2">' . $dataCliente[0]['rucdni'] . '</td>' .
                                    '</tr>' .
                                    '<tr>' .   
                                        '<th>Fecha Registro: </th>' .
                                        '<td>' . $dataRecepcion[0]['fregistro'] . '</td>' .
                                        '<th>Fecha Aprobacion: </th>' .
                                        '<td>' . $dataRecepcion[0]['fremision'] . '</td>' .
                                    '</tr>' .
                                    '<tr>' .
                                        '<th>Direccion: </th>' .
                                        '<td colspan="3">' . $dataCliente[0]['direccion'] . ' - ' . $dataCliente[0]['ubigeo'] . ' | <i>' . $dataCliente[0]['zonacategoria'] . '</i></td>' .
                                        '<th>Celular: </th>' .
                                        '<td>' . $dataCliente[0]['celular'] . '</td>' .
                                    '</tr>';
            if ($dataRecepcion[0]['idagencia'] > 0) {
                $transporte = new Transporte();
                $dataTransporte = $transporte->buscarxId($dataRecepcion[0]['idagencia']); 
                if (count($dataTransporte) > 0) {
                    $archivoConfig = parse_ini_file("config.ini", true);
                    $Responsable = $archivoConfig['Responsable'];
                    $acumuladorCabecera .= '<tr>' .                    
                                                '<th>Razon Social Agencia: </th>' .
                                                '<td>' . $dataTransporte[0]['trazonsocial'] . '</td>' .
                                                '<th>Direccion Agencia: </th>' .
                                                '<td>' . $dataTransporte[0]['tdireccion'] . '</td>' .
                                                '<th>Guia de Remsion: </th>' .
                                                '<td>' . $dataRecepcion[0]['serie'] . ' - ' . $dataRecepcion[0]['correlativo'] . '</td>' .
                                            '</tr>' . 
                                            '<tr>' .  
                                                '<th>Costo de Envio: </th>' .
                                                '<td>S/ ' . number_format($dataRecepcion[0]['importe'], 2) . '</td>' .  
                                                '<th>Nota: </th>' .
                                                '<td colspan="3">El ' . $dataRecepcion[0]['porcentaje'] . '% del costo de envio es asumido por <b>La Empresa</b>' . ($dataRecepcion[0]['porcentaje'] != 100 && !empty($Responsable[$dataRecepcion[0]['responsable']]) ? ', la diferencia sera asumido por <b>'. $Responsable[$dataRecepcion[0]['responsable']] . '</b>' : '') . '.</td>' .
                                            '</tr>';
                }
            }            
            for ($i = 0; $i < $tam; $i++) {
                $dataProducto = $atcliente->productoxDetalleordenventa($dataDetalles[$i]['iddetalleordenventa'], $dataRecepcion[0]['idcliente']);
                $simbolo = "S/ ";
                if ($dataProducto[$i]['idmoneda'] == 2) $simbolo = "US $ ";
                $acumuladorDetalle .= "<tr>" .
                                        '<td>' . $dataProducto[0]['nombrevendedor'] . '</td>' .
                                        "<td>" . $dataProducto[0]['codigov'] . "</td>" . 
                                        "<td>" . $dataProducto[0]['fordenventa'] . "</td>" .
                                        "<td>" . $dataProducto[0]['codigopa'] . "</td>" .
                                        "<td>" . $dataProducto[0]['nompro'] . "</td>" .
                                        "<td style='text-align: right;'>" . $simbolo . number_format($dataProducto[0]['preciofinal'], 2) . "</td>" .
                                        "<td style='text-align: right;'>" . $dataDetalles[$i]['cantidad'] . "</td>" .
                                        "<td style='text-align: center;'>" . ($dataDetalles[$i]['garantia'] == 1 ? "<img src='/imagenes/correcto.png'>" : "") . "</td>" .
                                        "<td>" . $dataDetalles[$i]['observaciones'] . "</td>" .
                                    "</tr>";
            }            
        }
        $resp['cabecera'] = $acumuladorCabecera;
        $resp['detalle'] = $acumuladorDetalle;
        $resp['codigost'] = $codigoST;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }
    
    function listarrecojostotal() {
        $atcliente = new Atencioncliente();
        $contador = 0;
        $data = array();
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        session_start();
        $_SESSION['P_Recojo'] = "";
        $dataRecojos = $atcliente->listaRecojosPaginado($pagina, "");        
        $paginacion = $atcliente->paginadoRecojos("");
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['dataRecojos'] = $dataRecojos;
        $this->view->show('/atencioncliente/listarrecojostotal.phtml', $data);
    }
    
    function buscaRecojos() {
        $atcliente = new Atencioncliente();
        $contador = 0;
        $data = array();
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        session_start();
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_Recojo'] = $_REQUEST['txtBusqueda'];
        }
        
        $parametro = $_SESSION['P_Recojo'];
        $dataRecojos = $atcliente->listaRecojosPaginado($pagina, $parametro);
        $paginacion = $atcliente->paginadoRecojos($parametro);
        
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['dataRecojos'] = $dataRecojos;
        $data['retorno'] = $parametro;
        $data['totregistros'] = $atcliente->cuentaRecojos("", $parametro);
        $this->view->show('/atencioncliente/buscarecojo.phtml', $data);
    }
    
    function seguridad() {
        $this->view->show("/atencioncliente/seguridad.phtml", $data);
    }
    
    function seguridad_consultar() {
        $fechainicio = $_REQUEST['txtFechaAprobadoInicio'];
        $fechafin = $_REQUEST['txtFechaAprobadoFin'];
        $idcliente = $_REQUEST['idCliente'];
        $estado = $_REQUEST['cmbEstado'];
        $atcliente = new Atencioncliente();
        $dataRecepciones = $atcliente->listadoRecepciones($fechainicio, $fechafin, $idcliente, $estado);
        $tamanio = count($dataRecepciones);
        for ($i = 0; $i < $tamanio; $i++) {
            echo '<tr>' .
                    '<td style="text-align:center">' . ($dataRecepciones[$i]['descargado'] == 0 ? ' -' : '#' . $dataRecepciones[$i]['descargado']) . '</td>' .
                    '<td>' . $dataRecepciones[$i]['codigost'] . '</td>' .
                    '<td>' . $dataRecepciones[$i]['fremision'] . '</td>' .
                    '<td>' . $dataRecepciones[$i]['codigov'] . '</td>' .
                    '<td>' . $dataRecepciones[$i]['razonsocial'] . '</td>' .
                    '<td>' . (!empty($dataRecepciones[$i]['ruc']) ? $dataRecepciones[$i]['ruc'] : $dataRecepciones[$i]['dni']) . '</td>' .
                    '<td>' . $dataRecepciones[$i]['codigopa'] . '</td>' .
                    '<td>' . $dataRecepciones[$i]['nompro'] . '</td>' .
                    '<td>' . $dataRecepciones[$i]['cantidad'] . '</td>' .
                    '<td>' . $dataRecepciones[$i]['nombremotivo'] . '</td>' .
                 '</tr>';   
        }
    }
    
}

?>