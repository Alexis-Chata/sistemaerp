<?php

Class seguimientoController extends ApplicationGeneral {

    function seguridad() {
        $this->view->show('/seguimiento/seguridad.phtml', $data);
    }

    function seguro() {
        $id = $_REQUEST['idOrdenVenta'];
        $model = $this->AutoLoadModel('seguimiento');
        $pdf = $this->AutoLoadModel('pdf');
        $filtro = "wc_ordenventa.idordenventa=$id";
        $Factura = $model->listaOrdenVenta($filtro);
        $seguimiento = $model->listadoSeguimiento($id);
        $datos['seguimiento'] = $seguimiento;
        $datos['factura'] = $Factura;
        echo json_encode($datos);
    }

    function updateSeguimiento() {
        $idordenventa = $_REQUEST['idordenventa'];
        $data['idordenventa'] = $idordenventa;
        $data['confirmacion'] = $_REQUEST['valor'];
        $seguimiento = $this->AutoLoadModel('seguimiento');
        $resultado = $seguimiento->grabaOrdenventa($data);
        echo json_encode($resultado);
    }

    function updateConfirmacion() {
        $idordenventa = $_REQUEST['idordenventa'];
        $campo = $_REQUEST['campo'];
        $data['idordenventa'] = $idordenventa;
        $data['confirmacionentrega'] = 1;
        $seguimiento = $this->AutoLoadModel('seguimiento');
        $rows = $seguimiento->listadoSeguimiento($idordenventa);
        if (count($rows)) {
            $valor = $rows[0]['confirmacionentrega'];
            $resultado = $seguimiento->UpdateConfirmacion($idordenventa, $campo, $valor);
        } else {
            $respuesta = $seguimiento->grabaOrdenventa($data);
        }
    }

    function listarSeguimiento() {
        $model = $this->AutoLoadModel('seguimiento');
        $fecha = date('Y-m-d', strtotime($_REQUEST['fechaSeguimiento']));
        $fechainicio = "'$fecha 00:00:00'";
        $fechafin = "'$fecha 23:59:59'";
        $filtro = "s.fechacreacion >= $fechainicio and s.fechacreacion <= $fechafin and s.estado=1";
        $data = $model->ListaDespachos($fecha, $filtro);
        $entregados = 0;
        $retornados = 0;
        $anulados = 0;
        $entregadoDevol = 0;
        $total = 0;
        echo "<thead>
                <tr>
                    <th colspan='2'><h2>FECHA: " . $fecha . "</h2></th>
		</tr>
                <tr>
                    <th>GUIA VENTA</th>
                    <th>SEGUIMIENTO</th>
                </tr>
             </thead>
             <tbody>";
        for ($i = 0; $i <= count($data); $i++) {
            echo "<tr>";
            echo    "<td class='center'>" . $data[$i]['codigov'] . "</td>";
            echo    "<td class='center'>";
            if (($data[$i]['confirmacion']) != "") {
                //if ($data[$i]['confirmacion'] == "E1" || $data[$i]['confirmacion'] == "E2" || $data[$i]['confirmacion'] == "E3") {
                if (!(strpos($data[$i]['confirmacion'], 'ED') === false)) {
                    echo"<stron>ENTREGADO CON DEVOLUCION</strong>";
                    $entregadoDevol+=1;
                } elseif (!(strpos($data[$i]['confirmacion'], 'R') === false)) {
                    echo"<strong>RETORNADO</strong>";
                    $retornados+=1;
                } elseif (!(strpos($data[$i]['confirmacion'], 'A') === false)) {
                    echo"<strong>ANULADO</strong>";
                    $anulados+=1;
                } elseif (!(strpos($data[$i]['confirmacion'], 'E') === false)) {
                    echo"<strong>ENTREGADO</strong>";
                    $entregados+=1;
                }
            }
            echo    "</td>";
            echo "</tr>";
        }
        echo    "<tr style='height:22px'></tr>";
        $total = $entregados + $retornados + $anulados + $entregadoDevol;
        echo "</tbody>";
        echo "<tfoot>
                <tr><td></td><td><center><span class='seguimiento'>ENTREGADOS : " . $entregados . "</span></center></td></tr>
                    <tr><td></td><td><center><span class='seguimiento'>ENTREGADOS CON DEVOLUCION : " . $entregadoDevol . "</span><center></td></tr>
                <tr><td></td><td><center><span class='seguimiento'>RETORNADOS : " . $retornados . "</span><center></td></tr>
                <tr><td></td><td><center><span class='seguimiento'>ANULADOS : " . $anulados . "</span><center></td></tr>                    
                <tr><td></td><td><center><span class='seguimento'>TOTAL :" . $total . "</span><center></td></tr>
             </tfoot>";
    }

    function confirmacionEntrega() {
        session_start();
        if (!empty($_REQUEST['idOrdenVenta'])) {
            
        }
        $model = $this->AutoLoadModel('pdf');
        $ordencobro = $this->AutoLoadModel('ordencobro');
        $Factura = $model->listaOrdenVentaPaginado($id, $filtro);
        for ($i = 0; $i < count($Factura); $i++) {
            $documento = $model->listaFacturaEmitidas($Factura[$i]['idordenventa']);
            $Factura[$i]['nombreTranporte'] = $model->nombretransporte($Factura[$i]['idclientetransporte']);
            $Factura[$i]['importeguia'] = $ordencobro->deudatotal($Factura[$i]['idordenventa']);
            $Factura[$i]['deuda'] = $ordencobro->totalPendiente($Factura[$i]['idordenventa']);
            if (!empty($documento) && count($documento) == 1) {
                $Factura[$i]['serie'] = $documento[0]['serie'];
                $Factura[$i]['numdoc'] = $documento[0]['numdoc'];
                $Factura[$i]['montofacturado'] = $documento[0]['montofacturado'];
                $Factura[$i]['nombredoc'] = $documento[0]['nombredoc'];
                $Factura[$i]['iddocumento'] = $documento[0]['iddocumento'];
            }
        }
        $data['Factura'] = $Factura;
        $paginacion = $model->paginadoOrdenVenta("");
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show('/seguimiento/confirmacionEntrega.phtml', $data);
    }
    
    function listarOVobservacion(){
        $fechaSeguimiento = $_REQUEST['fechaSeguimiento'];
        session_start();
        $ov = $this->AutoLoadModel('ordenventa');
        $data = $ov->listadoObservacion($fechaSeguimiento);
        echo "<table>";
        echo    "<thead>
                    <tr>
                        <th colspan='10' style='background:#B4D1F7;'><h2>FECHA: " . $fechaSeguimiento . "</h2></th>
                    </tr>
                    <tr>
                        <td style='background:#C6DCF9;'><b>Codigo</b></td>
                        <td style='background:#C6DCF9;'><b>Nombre Completo</b></td>
                        <td style='background:#C6DCF9;'><b>Razon Social</b></td>
                        <td style='background:#C6DCF9;'><b>Fecha</b></td>
                        <td style='background:#C6DCF9;'><b>Tipo de Venta</b></td>
                        <td style='background:#C6DCF9;'><b>Importe</b></td>
                        <td style='background:#C6DCF9;'><b>Importe Pagado</b></td>
                        <td style='background:#C6DCF9;'><b>Importe Devuelto</b></td>
                        <td style='background:#C6DCF9;'><b>Deuda</b></td>
                        <td style='background:#C6DCF9;'><b>Observacion</b></td>
                    </tr>
                </thead>
                <tbody>";
        for ($i = 0; $i < count($data); $i++) {
            echo "<tr>";
            echo    "<td>" . $data[$i]['codigov'] . "</td>";
            echo    "<td>" . $data[$i]['nombrecompleto'] . "</td>";
            echo    "<td>" . $data[$i]['razonsocial'] . "</td>";
            echo    "<td>" . $data[$i]['fordenventa'] . "</td>";
            echo    "<td>" . $data[$i]['moneda'] . "</td>";
            echo    "<td>" . number_format($data[$i]['importeov'], 2) . "</td>";
            echo    "<td>" . number_format($data[$i]['importepagado'], 2) . "</td>";
            echo    "<td>" . number_format($data[$i]['importedevolucion'], 2) . "</td>";
            echo    "<td>";
            if ($data[$i]['deuda'] < 0) {
                echo "0.00";
            } else {
                echo number_format($data[$i]['deuda'], 2);
            }
            echo    "</td>";
            echo    "<td class='center'>" . $data[$i]['observacion'] . "</td>";
            echo "</tr>";
        }
        if($i == 0) {
            echo "<tr>";
            echo    "<td colspan='10'>NO SE ENCONTRO REGISTRO</td>";
            echo "</tr>";
        }
        echo    "</tbody>";
        echo "</table>";
        echo "<ul>";
        echo    "<li>";
        echo        "<label>TOTAL DE REGISTROS ENCONTRADOS: " . count($data) . "</label>";
        echo    "</li>";
        echo "</ul>";
    }

    function correlativoguias() { 
        $id = $_REQUEST['id'];
        if (empty($id)) {
            $id = 1;
        }
        session_start();
        $ordven = $this->AutoLoadModel('ordenventa');
        $ov = $ordven->listarOrdenes($id);
        $total = count($ov);
        $resp = "";
        for ($i=0; $i < $total; $i++) {
            $resp .= '<tr' . (($ov[$i]['desaprobado']==1) ? ' style="background:#FF8383"' : '') . '>'
                    . '<td><b><a href="#cabeceraOV" data-urlredireccion="/ordencobro/buscarDetalleOrdenCobro/' . $ov[$i]['idordenventa'] . '" class="btnVerDetalleOrden">' . $ov[$i]['codigov'] . '<input type="hidden" value="' . $ov[$i]['codigov'] . '" class="codigov"></a></b></td>'
                    . '<td>' . $ov[$i]['nombrecompleto'] . '</td>'
                    . '<td>' . $ov[$i]['razonsocial'] . '</td>'
                    . '<td>' . $ov[$i]['fordenventa'] . '</td>'
                    . '<td>' . $ov[$i]['moneda'] . '</td>';
            if($ov[$i]['desaprobado']==1) {
                $resp .= '<td colspan="4"><b>DESAPROBADO</b></td>';
            } else {
                $guia = $this->AutoLoadModel('ordengasto');
                $ov[$i]['importeov'] = $guia->totalGuia($ov[$i]['idordenventa']);
                $ov[$i]['deuda'] = $ov[$i]['importeov'] - $ov[$i]['importepagado'];
                $resp .= '<td>' . number_format($ov[$i]['importeov'], 2) . '</td>'
                        . '<td>' . number_format($ov[$i]['importepagado'], 2) . '</td>'
                        . '<td>' . number_format($ov[$i]['importedevolucion'], 2) . '</td>'
                        . '<td>' . (($ov[$i]['deuda']<0) ? "0.00" : number_format($ov[$i]['deuda'], 2)) . '</td>';
            }
            $resp .= '<td><input type="text" maxlength="100" value="CORRECTO" class="Observacion" data-idordenventa="' . $ov[$i]['idordenventa'] . '" data-id="' .$ov[$i]['codigov'] . '" id="' . $ov[$i]['codigov'] . '"></td>'
                    . '</tr>';
        }
        $data['resp'] = $resp;
        $paginacion = $ordven->paginarOrdenes();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 30);
        $this->view->show('/seguimiento/correlativoguias.phtml', $data);
    }
    
    function registrarobservacion(){
        $IDOV = $_REQUEST['IDOV'];
        $observacion = $_REQUEST['TXTObservacion'];
        if(!empty($IDOV)){
            $ov = $this->AutoLoadModel('ordenventa');
            $data['idordenventa'] = $IDOV;
            $data['observacion'] = $observacion;
            $rsptaVer = $ov->verificarObservacion($IDOV);
            if(empty($rsptaVer->observacion)){
                $rspta = $ov->registrarObservacionOrden($data);
                if($rspta) {
                    $datos['rspta'] = 1;
                    $datos['observacion'] = $observacion;          
                } else {
                    $datos['rspta'] = 0;
                }
            } else {
               $datos['rspta'] = -1;
               $datos['observacion'] = $rsptaVer->observacion;
            }
            echo json_encode($datos);
        }
    }
    
    function letraxguia() {
        $this->AutoLoadModel('reporte');
        $this->view->show('/seguimiento/letrasxguia.phtml');
    }
    
    function verOrdenVentaxnroLetra() {
        $nroletras = $_REQUEST['numeroLetra'];
        $reporte = $this->AutoLoadModel('reporte');
        $nroletra = explode("\n", $nroletras);
        $tam = count($nroletra);
        for ($i = 0; $i < $tam; $i++) {
            $nroletra[$i] = trim($nroletra[$i]);
            if (!empty($nroletra[$i])) {
               $data = $reporte->letrasXordenventa($nroletra[$i]);
                $resul .= $data[0]['codigov'] . " " . $data[0]['numeroletra'] . "\n"; 
            }
        }
        $resultado['resultado'] = $resul;
        echo json_encode($resultado);
    }

    /*09-03-2017*/
    function listavales(){
        $this->view->show('/seguimiento/listavales.phtml');
    }
    
    function verlistavalesxOrdenventa() {
        $seguimiento = new Seguimiento();
        $nroletras = $_REQUEST['numeroLetra'];
        $reporte = $this->AutoLoadModel('reporte');
        $nroletra = explode("\n", $nroletras);
        $tam = count($nroletra);
        echo '<table>'
                . '<thead>'
                    . '<th>N</th>'
                    . '<th>Orden Venta</th>'
                    . '<th>Nombre Cliente</th>'
                    . '<th>Nombre Vendedor</th>'
                . '</thead>'
                . '<tbody>';
        for ($i = 0; $i < $tam; $i++) {
            $nroletra[$i] = trim($nroletra[$i]);
            if(!empty($nroletra[$i])){
                $respuesta = $seguimiento->verlistavalesxOrdenventa($nroletra[$i]);
                if(count($respuesta)==1){
                    echo  '<tr>'
                            . '<td>'.($i+1).'</td>'
                            . '<td>'.$respuesta[0]['codigo'].'</td>'
                            . '<td>'.$respuesta[0]['cliente'].'</td>'
                            . '<td>'.$respuesta[0]['vendedor'].'</td>'
                        . '</tr>';
                }else{
                    echo '<tr ><td>'.($i+1).'</td><td colspan="3">El código <b style="color:red;">'.$nroletra[$i].'</b> no se encuentra, verifique el codigo o llame a sistemas</td></tr>';
                }
            }
        }
        echo '</tbody>'
        . '</table>';
    }
    function verlistavalesxOrdenventaexcel() {
//        header('Content-type: application/vnd.ms-excel');
//        header("Content-Disposition: attachment; filename=lista".date('YmdHis').".xls");
//        header("Pragma: no-cache");
//        header("Expires: 0");
        $seguimiento = new Seguimiento();
        $nroletras = $_POST['numeroLetra'];
        var_dump($nroletras);
        $nroletra = explode("\n", $nroletras);
        $tam = count($nroletra);
        echo '<table>'
                . '<thead>'
                    . '<th>N</th>'
                    . '<th>Orden Venta</th>'
                    . '<th>Nombre Cliente</th>'
                    . '<th>Nombre Vendedor</th>'
                . '</thead>'
                . '<tbody>';
        for ($i = 0; $i < $tam; $i++) {
            $nroletra[$i] = trim($nroletra[$i]);
            if(!empty($nroletra[$i])){
                $respuesta = $seguimiento->verlistavalesxOrdenventa($nroletra[$i]);
                if(count($respuesta)==1){
                    echo  '<tr>'
                            . '<td>'.($i+1).'</td>'
                            . '<td>'.$respuesta[0]['codigo'].'</td>'
                            . '<td>'.$respuesta[0]['cliente'].'</td>'
                            . '<td>'.$respuesta[0]['vendedor'].'</td>'
                        . '</tr>';
                }else{
                    echo '<tr ><td>'.($i+1).'</td><td colspan="3">El código <b style="color:red;">'.$nroletra[$i].'</b> no se encuentra, verifique el codigo o llame a sistemas</td></tr>';
                }
            }
        }
        echo '</tbody>'
        . '</table>';
    }
    /*09-03-2017*/

    function pedidosentregados() {
        $this->view->show('/seguimiento/pedidosentregados.phtml');
    }
    
    function productomasvendido() {
        $this->view->show('/seguimiento/productomasvendido.phtml');
    }

    function devolucionesxmeses() {
        $this->view->show('/seguimiento/devolucionesxmeses.phtml');
    }

}


?>
