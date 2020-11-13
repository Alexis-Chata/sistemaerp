<?php

class InventarioController extends ApplicationGeneral {

    function productoInventario() {
        $inventario = $this->AutoLoadModel('inventario');
        $bloques = $this->AutoLoadModel('bloques');
        $data['inventario'] = $inventario->listadoConFecha();
        $data['bloques'] = $bloques->listado();
        $this->view->show('/inventario/productoInventario.phtml', $data);
    }

    function productoInventarioPart2() {
        $inventario = $this->AutoLoadModel('inventario');
        $bloques = $this->AutoLoadModel('bloques');
        $actor = $this->AutoLoadModel('actorrol');
        $data['inventario'] = $inventario->listadoConFecha();
        $data['bloques'] = $bloques->listado();
        $filtro = "ar.idrol!=30 and a.estado=1";
        $data['actor'] = $actor->actoresxfiltro($filtro);
        $data['auxiliar'] = $actor->actoresxRol(30);
        $this->view->show('/inventario/productoInventarioPart2.phtml', $data);
    }

    function cargaBloque() {
        $detalleInventario = $this->AutoLoadModel('reporte');
        $idBloque = $_REQUEST['idBloque'];
        $idInventario = $_REQUEST['idInventario'];
        $data = $detalleInventario->reporteInventario($idInventario, $idBloque, "");
        $cantidadDetalle = count($data);
        $fila = "";
        $fila .= "<tr>";
        $fila .=    "<th>N°</th>";
        $fila .=    "<th>Codigo</th>";
        $fila .=    "<th>Descripcion</th>";
        $fila .=    "<th>Inventario</th>";
        $fila .=    "<th>Bloque</th>";
        $fila .=    "<th>Buenos</th>";
        $fila .=    "<th style='font-size:11px !important;'>Mal Estado</th>";
        $fila .=    "<th style='font-size:11px !important;'>Para Serv.Tecnico</th>";
        $fila .=    "<th style='font-size:11px !important;'>En Show Room</th>";
        $fila .=    "<th>Total</th>";
        $fila .=    "<th>Observaciones</th>";
        $fila .=    "<th colspan='2'>Acciones</th>";
        $fila .= "</tr>";
        for ($i = 0; $i < $cantidadDetalle; $i++) {
            $fila .= "<tr>";
            $fila .=    "<td>" . ($i + 1) . "<input type='hidden' value='" . $data[$i]['iddetalleinventario'] . "' class='id'><input type='hidden' value='" . $data[$i]['idproducto'] . "' class='idProducto'></td>";
            $fila .=    "<td style='font-size:11px;'>" . $data[$i]['codigopa'] . "</td>";
            $fila .=    "<td style='font-size:11x;'>" . substr($data[$i]['nompro'], 0, 40) . "</td>";
            $fila .=    "<td style='font-size:10px;'>" . $data[$i]['codigoinv'] . "</td>";
            $fila .=    "<td style='font-size:11px;text-align:center !important;'>" . $data[$i]['codigo'] . "</td>";
            $readonly = "";
            $style = "";
            $css = "style='width:40px;'";
            if (!empty($data[$i]['usuariograbacion'])) {
                $readonly = "readonly";
                $css = "style='border:none;background:silver;width:40px;'";
                $style = "style='display:none'";
            }
            $fila .=    "<td><input " . $css . " type='text' size='4px' class='buenos numeric' value=" . $data[$i]['buenos'] . "  " . $readonly . "></td>";
            $fila .=    "<td><input type='text' size='4px' class='malos numeric' value=" . $data[$i]['malos'] . " style='border:none; width:40px;' readonly='readonly'></td>";
            $fila .=    "<td><input type='text' size='4px' class='servicio numeric' value=" . $data[$i]['servicio'] . " style='border:none; width:40px; ' readonly='readonly'></td>";
            $fila .=    "<td><input type='text' size='4px' class='showroom numeric' value=" . $data[$i]['showroom'] . " style='border:none; width:40px;' readonly='readonly'></td>";
            $fila .=    "<td><input type='text' size='4px' class='total numeric' value=" . ($data[$i]['buenos'] + $data[$i]['malos'] + $data[$i]['servicio'] + $data[$i]['showroom']) . " style='border:none; width:40px;' readonly='readonly'></td>";
            $fila .=    "<td style='font-size:10px;'>" . substr($data[$i]['descripcion'], 0, 25) . "</td>";
            $fila .=    "<td><a href='#' " . $style . " class='btnGrabar' title='Grabar'><img style='display:block;margin:auto' src='/imagenes/grabar.gif' width='25' height='25' ></a></td>";
            $fila .=    "<td><a  href='#' title='Eliminar' rel='" . $data[$i]['iddetalleinventario'] . "'><img src='/imagenes/eliminar.gif' class='btnEliminar'></a></td>";
            $fila .= "</tr>";
        }
        echo $fila;
    }

    function editarInventario() {
        $inventario = $this->AutoLoadModel('inventario');
        $data['inventario'] = $inventario->listadoConFecha();
        $this->view->show('/inventario/editarInventario.phtml', $data);
    }

    function cuadregeneral() {
        $inventario = $this->AutoLoadModel('inventario');
        $bloques = $this->AutoLoadModel('bloques');
        $linea = new Linea();
        $almacen = new Almacen();
        $data['Linea'] = $linea->listadoLineas('idpadre=0');
        $data['Almacen'] = $almacen->listadoAlmacen();
        $data['inventario'] = $inventario->listadoConFecha();
        $data['bloques'] = $bloques->listado();
        $this->view->show('/inventario/cuadregeneral.phtml', $data);
    }

    function saldosiniciales() {
        $saldosIniciales = $this->AutoLoadModel('saldosIniciales');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        if (isset($_REQUEST['producto'])) {
            $filtro = 'and si.idproducto=' . $_REQUEST['txtIdProducto'];
        }
        if (isset($_REQUEST['stock'])) {
            $filtro = 'and si.cantidad1=0';
        }
        $data['saldosiniciales'] = $saldosIniciales->listaSaldosInicialesPaginado($_REQUEST['id'], $filtro);
        $paginacion = $saldosIniciales->paginadoSaldosIniciales($filtro);
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/inventario/listasaldosiniciales.phtml", $data);
    }

    function grabarSaldoInicial() {
        $saldosIniciales = $this->AutoLoadModel('saldosIniciales');
        $evaluarDuplicidad = $saldosIniciales->evaluarDuplicididad($_REQUEST['txtIdProducto'], '2013');
        if (count($evaluarDuplicidad) == 0) {
            $grabosaldoinicial = $saldosIniciales->insertarSaldoInicial($_REQUEST['txtIdProducto'], $_REQUEST['txtCantidad1'], $_REQUEST['txtCunitario'], $_REQUEST['txtTcambio'], 1, 1, '2013-01-01', $_SESSION['idactor'], date("Y-m-d H:i:s"));
            if ($grabosaldoinicial == TRUE) {
                $grabosaldoinicial = $saldosIniciales->insertarSaldoInicial($_REQUEST['txtIdProducto'], $_REQUEST['txtCantidad2'], $_REQUEST['txtCunitario'], $_REQUEST['txtTcambio'], 1, 1, '2013-07-01', $_SESSION['idactor'], date("Y-m-d H:i:s"));
            }
            echo "<input type='hidden' id='txtRespuesta' value='" . $grabosaldoinicial . "'>";
        }
        if (count($evaluarDuplicidad) == 2) {
            echo "<input type='hidden' id='txtRespuesta' value='2'>";
        }
        //listado
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        $data['saldosiniciales'] = $saldosIniciales->listaSaldosInicialesPaginado($_REQUEST['id']);
        $paginacion = $saldosIniciales->paginadoSaldosIniciales();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/inventario/listasaldosiniciales.phtml", $data);
    }

    function editarSaldoInicial() {
        $cadena = $_REQUEST['id'];
        $porciones = explode("|", $cadena);
        $idSaldoInicial = $porciones[0];
        $idproducto = $porciones[1];
        $saldosIniciales = $this->AutoLoadModel('saldosIniciales');
        $data = $saldosIniciales->listarSaldonicial($idproducto);
        $tamRegistros = count($data);
        for ($i = 0; $i < $tamRegistros; $i++) {
            if ($idSaldoInicial == $data[$i]['idsaldo']) {
                echo "<tr style='color:red;'>";
            } else {
                echo "<tr>";
            }
            echo "<td style='text-align: center !important;'><input type='hidden' class='txtIdSaldo' value=" . $data[$i]['idsaldo'] . " value=" . $data[$i]['idsaldo'] . ">" . $data[$i]['codigopa'] . "</td>";
            echo "<td style='text-align: center !important;'>" . $data[$i]['nompro'] . "</td>";
            echo "<td style='text-align: center !important;'>" . $data[$i]['fechasaldo'] . "</td>";
            if ($tamRegistros <= 2) {
                echo "<td style='text-align: center !important;'><input type='text' class='txtUpstock' value=" . $data[$i]['cantidad1'] . " size='8'/></td>";
                echo "<td style='text-align: center !important;'><input type='text' class='txtUpCunitario' value=" . $data[$i]['costounitario'] . " size='8' /></td>";
                echo "<td style='text-align: center !important;'><input type='button'  value='Actualizar' class='button btnActualizar'></td>";
            } else {
                echo "<td style='text-align: center !important;'>" . $data[$i]['cantidad1'] . "</td>";
                echo "<td style='text-align: center !important;'>" . $data[$i]['costounitario'] . "</td>";
            }
            echo "</tr>";
        }
    }

    function actualizarSaldoInicial() {
        $saldosIniciales = $this->AutoLoadModel('saldosIniciales');
        $actualizarSaldoInicial = $saldosIniciales->actualizarSaldoInicial($_REQUEST['idsaldo'], $_REQUEST['cantidad1'], $_REQUEST['costounitario'], $_REQUEST['tcambio'], $_SESSION['idactor'], date("Y-m-d H:i:s"));
        if ($actualizarSaldoInicial == 1) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }
        echo json_encode(array("variable" => $respuesta));
    }

    function verificaExistenciaSaldoInicial() {
        $saldosIniciales = $this->AutoLoadModel('saldosIniciales');
        $evaluarDuplicidad = $saldosIniciales->evaluarDuplicididad($_REQUEST['txtIdProducto'], '2013');
        if (count($evaluarDuplicidad) == 0) {
            $respuesta = "false";
        } else {
            $respuesta = "true";
        }
        echo json_encode(array("variable" => $respuesta));
    }

    function stockActualProductoBloques() {
        $inventario = $this->AutoLoadModel('inventario');
        $data = $inventario->stockActualProductoBloques($_REQUEST['txtIdInventario'], $_REQUEST['txtIdProducto']);
        echo json_encode($data);
    }

}

?>