<?php

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

function volcadoBloque($idinventario, $idproducto, $idbloque, $estado, $usuariocreacion, $fechacreacion, $coneccion) {
    $sql = "INSERT INTO `wc_detalleinventario`
(`idinventario`,
`idproducto`,
`idbloque`,
`estado`,
`usuariocreacion`,
`fechacreacion`)
VALUES
('" . $idinventario . "',
'" . $idproducto . "',
'" . $idbloque . "',
'" . $estado . "',
'" . $usuariocreacion . "',
'" . $fechacreacion . "');";
    $resultado = filtro($sql, $coneccion);
    return $resultado;
}

begin($coneccion);

$listaUltimoBloque = listaUltimoBloque(10, $coneccion);
echo count($listaUltimoBloque) . '<br><br><br><br><br>';

foreach ($listaUltimoBloque as $v) {
    $volcadoBloque = volcadoBloque(11, $v['idproducto'], $v['idbloque'], 1, 358, '2020-01-02', $coneccion);
}
commit($coneccion);
?>