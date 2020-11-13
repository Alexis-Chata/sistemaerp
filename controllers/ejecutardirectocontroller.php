<?php

$algoritmoInventario = 1;
$volcadoAgrupamientobloques = 0;
if ($algoritmoInventario == 1 and $volcadoAgrupamientobloques == 0) {

    $coneccion = mysql_connect("localhost", "root", "");
    mysql_select_db("bdcelestium", $coneccion);

    function filtro($sql, $coneccion) {
        $resultset = mysql_query($sql, $coneccion);
        return $resultset;
    }

    function begin($coneccion) {
        $resultset = mysql_query("begin;", $coneccion);
        return $resultset;
    }

    function commit($coneccion) {
        $resultset = mysql_query("commit;", $coneccion);
        return $resultset;
    }

    function rollback($coneccion) {
        $resultset = mysql_query("rollback;", $coneccion);
        return $resultset;
    }

    function listanocontemplado_DetalleInventario($idinventario, $coneccion) {
        $sql = "select idproducto,stockactual,preciolista,preciocosto
        from wc_producto_20000001
        where estado=1 and
        stockactual > 0 and
        idproducto not in (select idproducto from wc_detalleinventario where idinventario='" . $idinventario . "' and estado=1)";
        $resultado = filtro($sql, $coneccion);
        return $resultado;
    }

    function listaDetalleInventario($idinventario, $coneccion) {
        $sql = "SELECT det.iddetalleinventario,det.idinventario,pro.idproducto,pro.stockactual,det.stockanterior,det.actualizado
    FROM  wc_producto_20000001 pro,wc_detalleinventario det
    WHERE pro.idproducto=det.idproducto AND det.idinventario='" . $idinventario . "' AND det.estado=1 and det.actualizado=0";
        $resultado = filtro($sql, $coneccion);
        return $resultado;
    }

    function actualizaDetalleInventario($stockanterior, $iddetalleinventario, $coneccion) {
        $sql = "UPDATE wc_detalleinventario  SET stockanterior='" . $stockanterior . "',actualizado=1 WHERE iddetalleinventario='" . $iddetalleinventario . "' and estado=1";
        $resultado = filtro($sql, $coneccion);
        return $resultado;
    }

    function insertar_nocontemplado_cargado_DetalleInventario($stockactual, $idproducto, $idinventario, $coneccion) {
        $sql = "INSERT INTO wc_detalleinventario (`stockanterior`, `idproducto`, `idinventario`, `idbloque`, `actualizado`, `estado`) VALUES ('$stockactual', '$idproducto', '$idinventario', '70', '1', '2')";
        $resultado = filtro($sql, $coneccion);
        return $resultado;
    }

    function insertar_nocontemplado_nocargado_DetalleInventario($stockactual, $idproducto, $idinventario, $coneccion) {
        $sql = "INSERT INTO wc_detalleinventario (`stockanterior`, `idproducto`, `idinventario`, `idbloque`, `actualizado`, `estado`) VALUES ('$stockactual', '$idproducto', '$idinventario', '119', '1', '2')";
        $resultado = filtro($sql, $coneccion);
        return $resultado;
    }

    function actualizarSTOCKanterior_invewntario($coneccion) {
        $listaDetalleInventario = listaDetalleInventario(10, $coneccion);
        $cnGuardados = 0;
        $cnProductosDelInventario = 0;
        begin($coneccion);
        while ($atributo = mysql_fetch_assoc($listaDetalleInventario)) {
            $cnProductosDelInventario = $cnProductosDelInventario + 1;
            if ($atributo['actualizado'] == 0) {
                $actualiza = actualizaDetalleInventario($atributo['stockactual'], $atributo['iddetalleinventario'], $coneccion);
                $afected = mysql_affected_rows();
                $cnGuardados = $afected + $cnGuardados;
            }
        }
        echo 'todo el inventario                                    ' . $cnProductosDelInventario;
        echo '<br>';
        echo 'cantidad de actualizados en el stockanterior          ' . $cnGuardados . '<br>';
        commit($coneccion);
    }

    function ingresarproductosnocontemplados($coneccion) {
        $listaProductos = listanocontemplado_DetalleInventario(10, $coneccion);
        $cnGuardados = 0;
        $cnProductosDelInventario = 0;
        begin($coneccion);
        while ($atributo = mysql_fetch_assoc($listaProductos)) {
            $cnProductosDelInventario = $cnProductosDelInventario + 1;
            echo $atributo['preciocosto'] . '---' . $atributo['preciolista'] . '[]=';

            if ($atributo['preciocosto'] >= 0.01 and $atributo['preciolista'] >= 0.01) {
                insertar_nocontemplado_cargado_DetalleInventario($atributo['stockactual'], $atributo['idproducto'], 10, $coneccion);

                echo '&nbsp;&nbsp;&nbsp;cargado=>' . $cnProductosDelInventario . '<br>';
            } else {
                insertar_nocontemplado_nocargado_DetalleInventario($atributo['stockactual'], $atributo['idproducto'], 10, $coneccion);
                echo 'no cargado=>' . $cnProductosDelInventario . '<br>';
            }


            $afected = mysql_affected_rows();
            $cnGuardados = $afected + $cnGuardados;
        }
        echo 'todo el inventario                                    ' . $cnProductosDelInventario;
        echo '<br>';
        echo 'cantidad de actualizados en el stockanterior          ' . $cnGuardados . '<br>';
        commit($coneccion);
    }


 actualizarSTOCKanterior_invewntario($coneccion);
}

/*
if ($algoritmoInventario == 0 and $volcadoAgrupamientobloques == 1) {
    $coneccion = mysql_connect("localhost", "root", "");
    mysql_select_db("bdcelestium", $coneccion);

    function filtro($sql, $coneccion) {
        $resultset = mysql_query($sql, $coneccion);
        return $resultset;
    }

    function lisAsos2($resultado) {
        $row_array = array();
        while ($row = mysql_fetch_assoc($resultado)) {
            $row_array[] = array_map('utf8_encode', $row);
        }
        return $row_array;
        //se usa para que el json asincrono no de errores
    }

    function begin($coneccion) {
        $resultset = mysql_query("begin;", $coneccion);
        return $resultset;
    }

    function commit($coneccion) {
        $resultset = mysql_query("commit;", $coneccion);
        return $resultset;
    }

    function rollback($coneccion) {
        $resultset = mysql_query("rollback;", $coneccion);
        return $resultset;
    }

    function listaUltimoBloque($idinventario, $coneccion) {
        $sql = "select * from wc_detalleinventario where idinventario='" . $idinventario . "' and estado=1;";
        $resultado = filtro($sql, $coneccion);
        $lisAsos2 = lisAsos2($resultado);
        return $lisAsos2;
    }

    function volcadoBloque($idinventario, $idproducto, $idbloque, $stockactual, $precio, $estado, $usuariocreacion, $fechacreacion, $coneccion) {
        $sql = "INSERT INTO `wc_detalleinventario`
(`idinventario`,
`idproducto`,
`idbloque`,
stockactual,
precio,
`estado`,
`usuariocreacion`,
`fechacreacion`)
VALUES
('" . $idinventario . "',
'" . $idproducto . "',
'" . $idbloque . "',
'" . $stockactual . "',
'" . $precio . "',
'" . $estado . "',
'" . $usuariocreacion . "',
'" . $fechacreacion . "');";
        $resultado = filtro($sql, $coneccion);
        return $resultado;
    }

    begin($coneccion);

    $listaUltimoBloque = listaUltimoBloque(8, $coneccion);
    echo count($listaUltimoBloque) . '<br><br><br><br><br>';

    foreach ($listaUltimoBloque as $v) {
        $volcadoBloque = volcadoBloque(9, $v['idproducto'], $v['idbloque'], $v['stockactual'], $v['precio'], 1, 358, '2018-11-23', $coneccion);
    }
    commit($coneccion);
}


*/