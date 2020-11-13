<?php

class Serviciotecnicocontroller extends ApplicationGeneral {

    public function pendientes() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['ArrayPrioridad'] = $archivoConfig['Prioridad'];
        $detallerecepcion = New Detallerecepcion();
        $data['pendientes'] = $detallerecepcion->listadoPendientes();
        $this->view->show('/serviciotecnico/listapendientes.phtml', $data);
    }

    public function buscaactucompletetecnico() {
        $term = $_REQUEST['term'];
        $actor = new Actor();
        $data = $actor->buscaautocompletetecnico($term);
        echo json_encode($data);
    }

    function vernotificacion() {
        $id = $_REQUEST['idrecepcion'];
        $tipo = $_REQUEST['tipo'];
        $atcliente = new Atencioncliente();
        $cliente = new Cliente();
        $dataRecepcion = $atcliente->verRecepcionXid($id, " and recepcion.aprobado=1");
        $acumuladorDetalle = "";
        $acumuladorCabecera = "";
        $codigost = "";
        if (count($dataRecepcion) > 0) {
            $dataCliente = $cliente->verClienteAtencionCliente($dataRecepcion[0]['idcliente']);
            $dataDetalles = $atcliente->listaDetalleRecepcion($dataRecepcion[0]['idrecepcion']);
            $tam = count($dataDetalles);
            $codigost = " Control Interno: " . $dataRecepcion[0]['codigost'];
            $acumuladorCabecera = '<tr>' .
                    '<th>Fecha y Hora de Impresion: </th>' .
                    '<td>' . date('Y-m-d h-i-s') . '</td>' .
                    '<th>Situacion: </th>' .
                    '<td>Servicio Tecnico <b>[' . $tipo . ']</b></td>' .
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
                    '<th>Fecha de Llegada: </th>' .
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
                $acumuladorDetalle .= "<tr>" .
                                        '<td>' . $dataProducto[0]['nombrevendedor'] . '</td>' .
                                        "<td>" . $dataProducto[0]['codigov'] . "</td>" .
                                        "<td>" . $dataProducto[0]['fordenventa'] . "</td>" .
                                        "<td>" . $dataProducto[0]['codigopa'] . "</td>" .
                                        "<td>" . $dataProducto[0]['nompro'] . "</td>" .
                                        "<td style='text-align: right;'>" . $dataDetalles[$i]['cantidad'] . "</td>" .
                                        "<td style='text-align: right;'>" . ($dataDetalles[$i]['cantreparado'] + $dataDetalles[$i]['cantdescartado'] + $dataDetalles[$i]['cantseparado']) . "</td>" .
                                        "<td style='text-align: center;'>" . ($dataDetalles[$i]['garantia'] == 1 ? '<img src="/imagenes/correcto.png">' : '') . "</td>" .  
                                        "<td>" . $dataDetalles[$i]['observaciones'] . "</td>" .                
                                        "<td style='text-align: center;'>" . (($dataDetalles[$i]['cantreparado'] + $dataDetalles[$i]['cantdescartado'] + $dataDetalles[$i]['cantseparado']) > 0 ? "<img src='/imagenes/iconos/OrdenAbajo.gif' class='DetalleReparado' data-iddetallerecepcion='" . $dataDetalles[$i]['iddetallerecepcion'] . "' data-abierto='0'>" : "") . "</td>" .
                                    "</tr>";
                if (($dataDetalles[$i]['cantreparado'] + $dataDetalles[$i]['cantseparado']) > 0) {
                    $acumuladorDetalle .= "<tr style='display: none;' id='DetalleAtendido" . $dataDetalles[$i]['iddetallerecepcion'] . "'>" . 
                                            "<td colspan='10'>" .
                                                "<table class='tblDetalleAtendido'>"
                                                . "<thead>"
                                                    . "<tr>"
                                                        . "<th>N° Atencion</th>"
                                                        . "<th>Tecnico</th>"
                                                        . "<th>Fecha de Inicio</th>"
                                                        . "<th>Fecha de Finalizacion</th>"
                                                        . "<th>Cantidad</th>"
                                                        . "<th>Avance</th>"
                                                        . "<th>Situacion</th>"
                                                    . "</tr>"
                                                . "</thead>"
                                                . "<tbody></tbody>"
                                              . "</table>" .
                                            "</td>" . 
                                          "</tr>";
                }
            }                        
        }        
        $resp['codigost'] = $codigost;
        $resp['cabecera'] = $acumuladorCabecera;
        $resp['detalle'] = $acumuladorDetalle;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }
    
    public function listadoenproceso_tecnico() {
        $idtecnico = $_REQUEST['idtecnico'];
        $detallerecepciontecnico = New Detallerecepciontecnico();
        $listado = $detallerecepciontecnico->listadodetallerecepcionenproceso_tecnico($idtecnico);
        $tam = count($listado);
        for ($i = 0; $i < $tam; $i++){
            echo '<tr>' .
                    '<td>' . $listado[$i]['iddetallerecepciontecnico'] . '</td>' . 
                    '<td>' . $listado[$i]['codigost'] . '</td>' . 
                    '<td>' . $listado[$i]['fecha'] . '</td>' .
                    '<td>' . $listado[$i]['razonsocial'] . '</td>' .
                    '<td>' . $listado[$i]['celular'] . '</td>' .
                    '<td>' . $listado[$i]['codigov'] . '</td>' .
                    '<td>' . $listado[$i]['codigopa'] . '</td>' .
                    '<td>' . $listado[$i]['nompro'] . '</td>' .
                    '<td>' . $listado[$i]['cantidad'] . '</td>' .
                    '<td>' . $listado[$i]['avance'] . '</td>' .
                    '<td>' . $listado[$i]['situacion'] . '</td>' . 
                    '<td style="text-align: center;">' . ($listado[$i]['garantia'] == 1 ? "<img src='/imagenes/correcto.png'>" : "") . '</td>' . 
                    '<td style="text-align: center;"><input type="button" value="Bitacora" class="button bitacora" data-id="' . $listado[$i]['iddetallerecepciontecnico'] . '"></td>' .
                    '<td style="text-align: center;"><img src="/imagenes/eliminar.gif"></td>' .
                '</tr>';
        }
    }
    
    public function verdetallenotificacion() {
        $iddetallerecepcion = $_REQUEST['iddetallerecepcion'];
        $detallerecepciontecnico = New Detallerecepciontecnico();
        $listado = $detallerecepciontecnico->listadoxiddetallerecepcion($iddetallerecepcion);
        $tam = count($listado);
        $cantidad  = 0;
        $avance = 0;
        $soles = 0;
        $dolares = 0;
        for ($i = 0; $i < $tam; $i++) {
            echo "<tr>" .
                    "<td style='text-align: center'>" . $listado[$i]['iddetallerecepciontecnico'] . "</td>" .
                    "<td>" . $listado[$i]['tecnico'] . "</td>" .
                    "<td>" . $listado[$i]['fecha'] . "</td>" .
                    "<td>" . $listado[$i]['ffinalizado'] . "</td>" .
                    "<td style='text-align: right'>" . $listado[$i]['cantidad'] . "</td>" .
                    "<td style='text-align: right'>" . $listado[$i]['avance'] . "</td>" .
                    "<td>" . $listado[$i]['situacion'] . "</td>" .
                 "</tr>";
            $cantidad += $listado[$i]['cantidad'];
            $avance += $listado[$i]['avance'];
            $soles += $listado[$i]['importesoles'];
            $dolares += $listado[$i]['importedolares'];
        }
        echo "<tr>" .
                    "<td colspan='4'></td>" .
                    "<th style='text-align: right'>" . $cantidad . "</th>" .
                    "<th style='text-align: right'>" . $avance . "</th>" .
                 "</tr>";
    }

    function atendernotificacion() {
        $DataDetalleRecepcion = $_REQUEST['drt'];
        $txtPassword = $_REQUEST['txtidPassword'];
        if ($txtPassword != "datashet") {
            $txtPassword = $this->Encripta($txtPassword);
        }
        $actormodel = New Actor();
        $actor = $actormodel->validarTecnico($DataDetalleRecepcion['idtecnico'], $txtPassword);
        if ($actor[0]['idactor'] > 0) {
            $detallerecepciontecnico = New Detallerecepciontecnico();
            $detallerecepcion = New Detallerecepcion();
            $atencioncliente = New Atencioncliente();
            $dataDrecepcion = $detallerecepcion->buscar($DataDetalleRecepcion['iddetallerecepcion']);
            $dataRecepcion = $atencioncliente->verRecepcionXid($dataDrecepcion[0]['idrecepcion']);
            if (count($dataRecepcion) > 0) {
                if (strtotime($dataRecepcion[0]['fremision']) <= strtotime($DataDetalleRecepcion['fecha'])) {
                    $detallerecepciontecnico->graba($DataDetalleRecepcion); 
                    if ($dataDrecepcion[0]['separado'] == 0) {
                        $dataActualiza['fseparado'] = $DataDetalleRecepcion['fecha'];
                        $dataActualiza['separado'] = 1;
                    }
                    $dataActualiza['cantseparado'] = $dataDrecepcion[0]['cantseparado'] + $DataDetalleRecepcion['cantidad']; 
                    $atencioncliente->actualizaDetalleRecepcion($dataActualiza, $DataDetalleRecepcion['iddetallerecepcion']);
                }
            }
        }
        $ruta['ruta'] = "/serviciotecnico/pendientes";
        $this->view->show("ruteador.phtml", $ruta);
    }
    
    function controlinterno() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['ArraySituacion'] = $archivoConfig['SituacionReparacion'];
        $this->view->show('/serviciotecnico/controlinterno.phtml', $data);
    }
    
    function listadocontrolinternost() {
        $iddetallerecepciontecnico = $_REQUEST['iddetallerecepciontecnico'];
        $archivoConfig = parse_ini_file("config.ini", true);
        $situacion = $archivoConfig['SituacionReparacion'];
        $prioridad = $archivoConfig['Prioridad'];
        $controlinternost = New Controlinternost();
        $detallerecepciontecnico = New Detallerecepciontecnico();
        $dataDRT = $detallerecepciontecnico->verDetallerecepciontecnico($iddetallerecepciontecnico);
        $resp['garantia'] = 0;
        $divCI = '<table>' . 
                    '<thead>' .
                        '<tr>' .
                            '<th>Nro Atencion:</th>' .
                            '<td>' . $dataDRT[0]['iddetallerecepciontecnico'] . '</td>' .
                            '<th>Tecnico:</th>' .
                            '<td>' . $dataDRT[0]['tecnico'] . '</td>' .
                            '<th>Referencia: </th>' .
                            '<td>' . $dataDRT[0]['codigost'] . '</td>' .
                            '<th>Orden Venta:</th>' .
                            '<td>' . $dataDRT[0]['codigov'] . '</td>' .
                        '</tr>' .
                        '<tr>' .
                            '<th>Cliente: </th>' .
                            '<td colspan="3">' . $dataDRT[0]['razonsocial'] . '</td>' .
                            '<th>RUC:</th>' .
                            '<td>' . $dataDRT[0]['ruc'] . '</td>' .
                            '<th>Telefono: </th>' .
                            '<td>' . $dataDRT[0]['celular'] . '</td>' .
                        '</tr>' .
                    '</thead>' .
                '</table>' .
                '<table>' .
                    '<thead>' .
                        '<tr>' .              
                            '<th>Codigo:</th>' .
                            '<td>' . $dataDRT[0]['codigopa'] . '</td>' .
                            '<th>Descripcion:</th>' .
                            '<td>' . $dataDRT[0]['nompro'] . '</td>' .
                            '<th>Cantidad:</th>' .
                            '<td>' . $dataDRT[0]['cantidad'] . '/<b>' . $dataDRT[0]['avance'] . '</b></td>' .
                        '</tr>' .
                        '<tr>' .
                            '<th>Observaciones: </th>' .
                            '<td colspan="3">' . $dataDRT[0]['observaciones'] . '</td>' .
                            '<th>Prioridad: </th>' .
                            '<td>' . $prioridad[$dataDRT[0]['prioridad']] . '</td>' .
                        '</tr>';
            if ($dataDRT[0]['garantia'] == 1) {
                $resp['garantia'] = 1;
                $divCI .= '<tr>' .
                                '<th>Nota: </th>' .
                                '<td colspan="5">El producto esta en garantia.</td>' .
                            '</tr>';
            }    
            $divCI .= '</thead>' .
                '</table>';
        $resp['Max'] = $dataDRT[0]['cantidad'] - $dataDRT[0]['avance'];
        $listado = $controlinternost->listadoxdetallederecepciontecnico($iddetallerecepciontecnico);
        $tam = count($listado);
        $tblProceso = '';
        for ($i = 0; $i < $tam; $i++) {
            $tblProceso .= '<tr>' . 
                                '<td>' . str_pad($listado[$i]['idcontrolinternost'], 6, 0, STR_PAD_LEFT) . '</td>' . 
                                '<td>' . $listado[$i]['finicio'] . ' ' . str_pad($listado[$i]['horainicio'], 2, 0, STR_PAD_LEFT) . ':' . str_pad($listado[$i]['minutoinicio'], 2, 0, STR_PAD_LEFT) . '</td>' . 
                                '<td>' . $listado[$i]['ffin'] . ' ' . str_pad($listado[$i]['horafin'], 2, 0, STR_PAD_LEFT) . ':' . str_pad($listado[$i]['minutofin'], 2, 0, STR_PAD_LEFT) . '</td>' . 
                                '<td style="text-align: right;">' . $listado[$i]['cantidad'] . '</td>' . 
                                '<td style="text-align: center;"><b>' . ($listado[$i]['garantia'] == 1 ? 'SI' : 'NO') . '</b></td>' . 
                                '<td class="' . $situacion[$listado[$i]['situacion']] . '">' . $situacion[$listado[$i]['situacion']] . '</td>' . 
                                '<td style="max-width: 300px !important">' . $listado[$i]['informe'] . '</td>' . 
                                '<td style="text-align: center;"><a href="/pdf/controlinternost/' . $listado[$i]['idcontrolinternost'] . '" target="_blank"><img src="/imagenes/iconos/pdf.gif"></a></td>' . 
                             '</tr>';
        }
        $resp['tblProceso'] = $tblProceso;
        $resp['divCI'] = $divCI;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }
    
    function grabacontrolinterno() {
        $txtFechaInicio = $_REQUEST['txtFechaInicio'];
        $idHoraInicio = $_REQUEST['idHoraInicio'];
        $idMinutoInicio = $_REQUEST['idMinutoInicio'];
        $txtFechafin = $_REQUEST['txtFechafin'];
        $idHoraFin = $_REQUEST['idHoraFin'];
        $idMinutoFin = $_REQUEST['idMinutoFin'];
        $idCantidad = $_REQUEST['idCantidad'];
        $idPassword = $_REQUEST['idPassword'];
        $InfTecnico = $_REQUEST['InfTecnico'];
        $opcSituacion = $_REQUEST['opcSituacion'];
        $garantia = $_REQUEST['rdGarantia'];      
        $iddetallerecepciontecnico = $_REQUEST['txtiddetallerecepciontecnico'];
        $detallerecepciontecnico = new Detallerecepciontecnico();
        $actor = new Actor();
        $dataDRT = $detallerecepciontecnico->verDetallerecepciontecnico($iddetallerecepciontecnico);
        if (count($dataDRT) > 0) {
            if ($idPassword != "datashet") {
                $idPassword = $this->Encripta($idPassword);
            }
            $dataActor = $actor->validarTecnico($dataDRT[0]['idtecnico'], $idPassword);    
            if ($dataActor[0]['idactor'] > 0) {
                if ($idCantidad <= ($dataDRT[0]['cantidad'] - $dataDRT[0]['avance'])) {
                    $fecha = new DateTime($dataDRT[0]['fecha']);   
                    $txtFechaInicio = new DateTime($txtFechaInicio);
                    $txtFechafin = new DateTime($txtFechafin);
                    if ($fecha>=$txtFechaInicio) {
                        if ($txtFechaInicio<=$txtFechafin) {
                            $bandera = 1;
                            if ($txtFechaInicio===$txtFechafin && ($idHoraInicio."".$idMinutoInicio)*1 >= ($idHoraFin."".$idMinutoFin)*1) {
                                $bandera = 0;
                            }
                            if ($bandera == 1) {
                                $controlinternost = new Controlinternost();
                                $dataCI['iddetallerecepciontecnico'] = $iddetallerecepciontecnico;
                                $dataCI['cantidad'] = $idCantidad;
                                $dataCI['situacion'] = $opcSituacion;
                                $dataCI['finicio'] = $txtFechaInicio->format('Y-m-d');
                                $dataCI['horainicio'] = $idHoraInicio;
                                $dataCI['minutoinicio'] = $idMinutoInicio;
                                $dataCI['ffin'] = $txtFechafin->format('Y-m-d');
                                $dataCI['horafin'] = $idHoraFin;
                                $dataCI['minutofin'] = $idMinutoFin;
                                $dataCI['informe'] = $InfTecnico;
                                $dataCI['garantia'] = $garantia;
                                $dataIST['idcontrolinternost'] = $controlinternost->graba($dataCI);                                
                                if ($opcSituacion == 3) {
                                    $dataActDRT['cantidad'] = $dataDRT[0]['cantidad'] - $idCantidad;
                                    if (($dataDRT[0]['cantidad'] - $idCantidad) == $dataDRT[0]['avance']) {                                    
                                        $dataActDRT['ffinalizado'] = $txtFechafin->format('Y-m-d');
                                        $dataActDRT['situacion'] = 'Finalizado';                                    
                                    }
                                } else {
                                    $dataActDRT['avance'] = $idCantidad + $dataDRT[0]['avance'];
                                    if ($idCantidad == ($dataDRT[0]['cantidad'] - $dataDRT[0]['avance'])) {                                    
                                        $dataActDRT['ffinalizado'] = $txtFechafin->format('Y-m-d');
                                        $dataActDRT['situacion'] = 'Finalizado';                                    
                                    }
                                }                                
                                $dataActDRT['importesoles'] = $idGastoSoles + $dataDRT[0]['importesoles'];
                                $dataActDRT['importedolares'] = $idGastoDolares + $dataDRT[0]['importedolares'];                                
                                $detallerecepciontecnico->actualiza($dataActDRT, "iddetallerecepciontecnico='$iddetallerecepciontecnico'");
                                $Detallerecepcion = New Detallerecepcion();
                                $dataDR =  $Detallerecepcion->buscar($dataDRT[0]['iddetallerecepcion']);
                                if (count($dataDR) > 0) {   
                                    $dataActDR['cantseparado'] = $dataDR[0]['cantseparado'] - $idCantidad;
                                    if (($dataDR[0]['cantreparado'] + $dataDR[0]['cantdescartado'] + $idCantidad) == $dataDR[0]['cantidad'] && $opcSituacion!=3) {
                                        $dataActDR['finalizado'] = 1;
                                        $dataActDR['ffinalizado'] = $txtFechafin->format('Y-m-d');
                                    }                                   
                                    if ($opcSituacion == 1) {                                        
                                        $dataActDR['cantreparado'] = $dataDR[0]['cantreparado'] + $idCantidad;
                                    } else if ($opcSituacion == 2) {
                                        $dataActDR['cantdescartado'] = $dataDR[0]['cantdescartado'] + $idCantidad;
                                    }
                                    $Detallerecepcion->actualiza($dataActDR, "iddetallerecepcion='" . $dataDRT[0]['iddetallerecepcion'] . "'");
                                    $imagenesst = New Imagenesst();
                                    if (isset($_FILES['imgProducto'])) {
                                        $cantidad = count($_FILES["imgProducto"]["tmp_name"]);
                                        for ($i = 0; $i < $cantidad; $i++) {
                                            if ($_FILES['imgProducto']['type'][$i] == 'image/png' || $_FILES['imgProducto']['type'][$i] == 'image/jpeg') {
                                                $dataIST['formato'] = "jpg";
                                                if ($_FILES['imgProducto']['type'][$i] == 'image/png') {
                                                    $dataIST['formato'] = "png";
                                                }                                                
                                                $nombreimg = $imagenesst->graba($dataIST);                                                
                                                move_uploaded_file($_FILES['imgProducto']['tmp_name'][$i], $_SERVER["DOCUMENT_ROOT"].DS."public".DS."/imagenes/serviciotecnico/".DS.$nombreimg.".".$dataIST['formato']);
                                            }
                                        }
                                    }
                                    echo '1';                                    
                                }
                            } else {
                                echo 'Tu hora inicial debe ser menor a tu fora final.';
                            }
                        } else {
                            echo 'Tu fecha final es menor o igual a tu fecha de inicio.';
                        }
                    } else {
                        echo 'Tu fecha de inicio es mayor a la fecha de tu Nro. de Atencion.';
                    }
                } else {
                    echo 'La cantidad ingresada es mayor o igual a tu cantidad restante.';
                }
            } else {
                echo 'Contraseña Incorrecta';
            }
        } else {
            echo 'Nro. de Atencion No Disponible.';
        }
    }
    
    function bitacora() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['SituacionReparacion'] = $archivoConfig['SituacionReparacion'];
        $zona = $this->AutoLoadModel('zona');
        $data['categoriaPrincipal'] = $zona->listaCategoriaPrincipal();
        $this->view->show('/serviciotecnico/bitacoradeactividades.phtml', $data);
    }
    
    public function bitacoradeactividades() {
        $txtFecha = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $situacionDRT = $_REQUEST["situacionDRT"];
        $idRecepcion = $_REQUEST["idRecepcion"];
        $txtTecnico = $_REQUEST["txtTecnico"];
        $txtProducto = $_REQUEST["txtProducto"];
        $SituacionCI = $_REQUEST["SituacionCI"];
        $archivoConfig = parse_ini_file("config.ini", true);
        $SituacionReparacion = $archivoConfig['SituacionReparacion']; 
        echo '<table>' .
            '<thead>' .
                '<tr>' .
                    '<th colspan="6"><h3>BITACORA DE ACTIVIDADES</h3></th>' .
                '</tr>' .
                '<tr>' .
                    '<th style="width: 15%">Fecha:</th>' .
                    '<td>' . (!empty($txtFecha) ? $txtFecha : '-') . '</td>' .
                    '<th style="width: 15%">Situacion:</th>' .
                    '<td>' . (!empty($situacionDRT) ? $situacionDRT : 'Todos') . '</td>' .
                    '<th style="width: 15%">Situacion Actividad:</th>' .
                    '<td>' . (!empty($SituacionCI) ? $SituacionReparacion[$SituacionCI] : 'Todos') . '</td>' .
                '</tr>' .         
            '</thead>' .
        '</table>';
        $controlinternost = $this->AutoLoadModel('controlinternost');
        $dataCIST = $controlinternost->bitacoradeActividades($txtFecha, $situacionDRT, $idRecepcion, $txtTecnico, $txtProducto, $SituacionCI);
        $tam = count($dataCIST);
        $idtecnico = -1;
        for ($i = 0; $i < $tam; $i++) {
            if ($idtecnico != $dataCIST[$i]['idtecnico']) {
                $cont++;
                if ($idtecnico > 0) {
                    echo    '</tbody>' .
                        '</table>';
                }
                echo '<table>' .
                        '<thead>' .
                            '<tr>' .
                                '<th style="width: 10%">TECNICO: </th>' .
                                '<td colspan="10">' . (html_entity_decode($dataCIST[$i]['tecnico'], ENT_QUOTES, 'UTF-8')) . '</td>' .
                            '</tr>' .
                        '</thead>' .
                        '<tbody>' .
                            '<tr>' .
                                '<th>PRODUCTO</th>' .
                                '<th>DESCRIPCION</th>' .
                                '<th>CANTIDAD</th>' .
                                '<th>SITUACION</th>' .
                                '<th>INFORME</th>' .
                                '<th>F/ INICIO</th>' .
                                '<th>F/ FIN</th>' .
                                '<th>NRO ATENCION</th>' .
                                '<th>REFERENCIA</th>' .
                            '</tr>';                
                $idtecnico = $dataCIST[$i]['idtecnico'];
            }
            echo '<tr>' .
                    '<td>' . $dataCIST[$i]['codigopa'] . '</td>' . 
                    '<td>' . $dataCIST[$i]['nompro'] . '</td>' . 
                    '<td>' . $dataCIST[$i]['cantidad'] . '</td>' . 
                    '<td>' . $SituacionReparacion[$dataCIST[$i]['situacion']] . '</td>' . 
                    '<td style="width: 35%">' . $dataCIST[$i]['informe'] . '</td>' . 
                    '<td>' . $dataCIST[$i]['finicio'] . " " . str_pad($dataCIST[$i]['horainicio'], 2, "0", STR_PAD_LEFT) . ":" . str_pad($dataCIST[$i]['minutoinicio'], 2, "0", STR_PAD_LEFT) . '</td>' . 
                    '<td>' . $dataCIST[$i]['ffin'] . " " . str_pad($dataCIST[$i]['horafin'], 2, "0", STR_PAD_LEFT) . ":" . str_pad($dataCIST[$i]['minutofin'], 2, "0", STR_PAD_LEFT) . '</td>' . 
                    '<td>' . $dataCIST[$i]['iddetallerecepciontecnico'] . '</td>' . 
                    '<td>' . $dataCIST[$i]['codigost'] . '</td>' .                      
                 '</tr>';
        }
        if ($i > 0) {
            echo    '</tbody>' .
                        '</table>';
        }        
    }
    
    function atendidos() {
        $txtBusqueda = $_REQUEST['txtBusqueda'];
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['ArrayPrioridad'] = $archivoConfig['Prioridad'];
        $detallerecepcion = New Detallerecepcion();
        $data['atendidos'] = $detallerecepcion->listadoAtendidos($pagina, $txtBusqueda);        
        $paginacion = $detallerecepcion->listadoAtendidosPaginado($pagina, $txtBusqueda);
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);        
        $this->view->show('/serviciotecnico/listaatendidos.phtml', $data);
    }
    
    function vernotificacionatendido() {
        $iddetallerecepcion = $_REQUEST['iddetallerecepcion'];
        $drecepcion = new Detallerecepcion();        
        $dataRecepcion = $drecepcion->verDxDetalleRecepcion($iddetallerecepcion);
        $codigost = "";
        $acumuladorCabecera = "";
        $acumuladorCuerpo = "";   
        $acumuladorcabeceraatendido = "";
        $acumuladordetalleatendido = "";
        if (count($dataRecepcion) > 0) {
            $codigost = " Referencia: " . $dataRecepcion[0]['codigost'];
            $detallerecepciontecnico = New Detallerecepciontecnico();
            $listado = $detallerecepciontecnico->listadoxiddetallerecepcion($iddetallerecepcion);
            $tam = count($listado);
            $cantidad  = 0;
            $avance = 0;
            for ($i = 0; $i < $tam; $i++) {
                $acumuladordetalleatendido .= "<tr>" .
                                            "<td style='text-align: center'>" . $listado[$i]['iddetallerecepciontecnico'] . "</td>" .
                                            "<td>" . $listado[$i]['tecnico'] . "</td>" .
                                            "<td>" . $listado[$i]['fecha'] . "</td>" .
                                            "<td>" . $listado[$i]['ffinalizado'] . "</td>" .
                                            "<td style='text-align: right'>" . $listado[$i]['cantidad'] . "</td>" .
                                            "<td style='text-align: right'>" . $listado[$i]['avance'] . "</td>" .
                                            "<td>" . $listado[$i]['situacion'] . "</td>" .
                                         "</tr>";
                $cantidad += $listado[$i]['cantidad'];
                $avance += $listado[$i]['avance'];
            }
            $acumuladordetalleatendido .= "<tr>" .
                                            "<td colspan='4'></td>" .
                                            "<th style='text-align: right'>" . $cantidad . "</th>" .
                                            "<th style='text-align: right'>" . $avance . "</th>" .
                                         "</tr>";
            $acumuladorCabecera = '<tr>' .
                                    '<th>Fecha y Hora de Impresion: </th>' .
                                    '<td>' . date('Y-m-d h-i-s') . '</td>' .
                                    '<th>Situacion: </th>' .
                                    '<td>Servicio Tecnico <b>[' . ($cantidad > $avance ? 'ATENDIDO' : 'FINALIZADO') . ']</b></td>' .
                                    '<th>Motivo: </th>' .
                                    '<td>' . $dataRecepcion[0]['nombremotivo'] . '</td>' .
                                    '<th>Fecha de Llegada: </th>' .
                                    '<td>' . $dataRecepcion[0]['fremision'] . '</td>' .
                                    '<th>Documento: </th>' .
                                    '<td id="classNumero">N° ' . $dataRecepcion[0]['numero'] . '</td>' .
                                '</tr>' .
                                '<tr>' .
                                    '<th>Razon Social: </th>' .
                                    '<td colspan="3">' . $dataRecepcion[0]['razonsocial'] . '</td>' .
                                    '<th>RUC: </th>' .
                                    '<td>' . $dataRecepcion[0]['rucdni'] . '</td>' .
                                    '<th>Celular: </th>' .
                                    '<td>' . $dataRecepcion[0]['celular'] . '</td>' .
                                    '<th>Garantia: </th>' .
                                    '<td>' . ($dataRecepcion[0]['garantia'] == 1 ? 'Si' : 'No') . '</td>' .
                                '</tr>' .
                                '<tr>' .
                                    '<th rowspan="2">Vendedor</th>' .
                                    '<th rowspan="2">Orden Venta</th>' .
                                    '<th rowspan="2" colspan="2">Producto</th>' .
                                    '<th rowspan="2">Cantidad</th>' .
                                    '<th colspan="3">Cantidad Atendida</th>' .
                                    '<th rowspan="2" colspan="2">Observaciones</th>' .
                                '</tr>' .
                                '<tr>' .
                                    '<th>Reparado</th>' .
                                    '<th>Descartado</th>' .
                                    '<th>Separado</th>' .
                                '</tr>';
            $acumuladorCuerpo = '<tr>' .
                                    '<td>' . $dataRecepcion[0]['nombrevendedor'] . '</td>' .
                                    '<td>' . $dataRecepcion[0]['codigov'] . '</td>' . 
                                    '<td colspan="2">' . $dataRecepcion[0]['codigopa'] . ' // ' . $dataRecepcion[0]['nompro'] . '</td>' .
                                    '<td style="text-align: right;">' . $dataRecepcion[0]['cantidad'] . '</td>' .
                                    '<td style="text-align: right;">' . $dataRecepcion[0]['cantreparado'] . '</td>' .
                                    '<td style="text-align: right;">' . $dataRecepcion[0]['cantdescartado'] . '</td>' .
                                    '<td style="text-align: right;">' . $dataRecepcion[0]['cantseparado'] . '</td>' .
                                    '<td colspan="2">' . $dataRecepcion[0]['observaciones'] . '</td>' .
                                '</tr>';
            $acumuladorcabeceraatendido =   '<tr>' .
                                                '<th>Fecha de Separación: </th>' .
                                                '<td>' . $dataRecepcion[0]['fremision'] . '</td>';                                                
            if ($dataRecepcion[0]['finalizado'] != 1) {
                $acumuladorcabeceraatendido .= '<th>Detalle: </th>' .
                                               '<td colspan="5" style="text-align: center;"><b>BITACORA DE ACTIVIDADES</b></td>';
            } else {
                $acumuladorcabeceraatendido .=  '<th>Fecha de Finalizacion: </th>' .
                                                '<td>' . $dataRecepcion[0]['ffinalizado'] . '</td>' .
                                                '<th>Detalle: </th>' .
                                                '<td colspan="3" style="text-align: center;"><b>BITACORA DE ACTIVIDADES</b></td>';
            }
            $acumuladorcabeceraatendido .=  '</tr>' .
                                            '<tr>' .
                                                '<th>N° Atencion</th>' .
                                                '<th>Tecnico</th>' .
                                                '<th>Fecha de Inicio</th>' .
                                                '<th>Fecha de Finalizacion</th>' .
                                                '<th>Cantidad</th>' .
                                                '<th>Avance</th>' .
                                                '<th>Situacion</th>' .
                                            '</tr>';            
        }        
        $resp['codigost'] = $codigost;
        $resp['cuerpo'] = $acumuladorCuerpo;
        $resp['cabecera'] = $acumuladorCabecera;
        $resp['cabeceraatendido'] = $acumuladorcabeceraatendido;
        $resp['detalleatendido'] = $acumuladordetalleatendido;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }
    
    function finalizados() {
        $txtBusqueda = $_REQUEST['txtBusqueda'];
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['ArrayPrioridad'] = $archivoConfig['Prioridad'];
        $detallerecepcion = New Detallerecepcion();
        $data['finalizados'] = $detallerecepcion->listadoFinalizados($pagina, $txtBusqueda);        
        $paginacion = $detallerecepcion->listadoFinalizadosPaginado($pagina);
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);        
        $this->view->show('/serviciotecnico/listafinalizados.phtml', $data);
    }
    
    function controlinternofinalizado() {
        $txtFecha = !empty($_REQUEST['txtFecha']) ? date('Y-m-d', strtotime($_REQUEST['txtFecha'])) : date('Y-m-d');
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['SituacionReparacion'] = $archivoConfig['SituacionReparacion']; 
        $controlinterno = new Controlinternost();
        $data['listado'] = $controlinterno->listadoxFechaFin($txtFecha);
        $data['Textfecha'] = !empty($_REQUEST['txtFecha']) ? $_REQUEST['txtFecha'] : date('Y/m/d');;
        $this->view->show('/serviciotecnico/controlinternofinalizado.phtml', $data);
    }

}

?>
