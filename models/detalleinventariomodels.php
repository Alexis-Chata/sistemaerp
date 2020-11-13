<?php

class detalleinventario extends Applicationbase {

    private $tabla = 'wc_detalleinventario';

    function listadodetalleInventarioxId($iddetalleinventario) {
        $movimiento = $this->leeRegistro($this->tabla, "", "iddetalleinventario='$iddetalleinventario'", "", "");
        return $movimiento;
    }

    function listado() {
        $data = $this->leeRegistro($this->tabla, "", "estado=1", "");
        return $data;
    }

    function buscaxId($iddetalleinventario) {
        $data = $this->leeRegistro($this->tabla, "", "iddetalleinventario='$iddetalleinventario' and estado=1", "");
        return $data;
    }

    function buscaxfiltro($filtro) {
        $data = $this->leeRegistro($this->tabla, "", $filtro, "");
        return $data;
    }

    function actualiza($data, $iddetalleinventario) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "iddetalleinventario=$iddetalleinventario");
        return $exito;
    }

    function actualizaxFiltro($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    function cambiaEstado($iddetalleinventario) {
        $exito = $this->inactivaRegistro($this->tabla, "iddetalleinventario=$iddetalleinventario");
        return $exito;
    }

    function graba($data) {
        $estado = $this->grabaRegistro($this->tabla, $data);

        return $estado;
    }

    function registro($tabla, $data) {
        $exito = $this->grabaRegistro($tabla, $data);
        return $exito;
    }

    function busca_producto_en_inventario_por_bloque($idinventario, $idproducto, $idbloque) {
        $data = $this->leeRegistro($this->tabla, "*", "idinventario='$idinventario' and idproducto='$idproducto' and idbloque='$idbloque' and estado in(1,2)", "");
        return $data;
    }

    function busca_producto_en_inventario_por_bloque_ver2($idinventario, $idproducto) {
        $data = $this->leeRegistro($this->tabla, "*", "idinventario='$idinventario' and idproducto='$idproducto'  and estado in(1,2)", "");
        return $data;
    }

    function actualizoDetalleInventario($data, $idinventario, $idproducto, $idbloque) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idinventario='$idinventario' and idproducto='$idproducto' and idbloque='$idbloque'");
        return $exito;
    }

    function verificarBloque_para_que_no_se_duplique_en_bloques_de_un_mismo_inventario($idinventario, $idproducto) {
        $sql = "SELECT bloq.`codigo` as bloque,bloq.`idbloque`
                FROM wc_detalleinventario  detInv,`wc_bloques` bloq
                WHERE detInv.idbloque=bloq.`idbloque`
                AND idinventario='" . $idinventario . "' AND idproducto='" . $idproducto . "' AND `detInv`.`estado`=1";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function stockSegunKardex($idproducto, $fecha) {
        $sql = "select det.stockactual  as cantidad from
                wc_detallemovimiento det
                Inner Join wc_movimiento mov On det.idmovimiento=mov.idmovimiento
                and mov.estado=1  and det.estado=1 
                and mov.fechamovimiento<'" . $fecha . "'  and det.idproducto='" . $idproducto . "' order by det.iddetallemovimiento desc limit 0,1;";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function stockCierre($idinventario, $idproducto) {
        $sql = "select stockanterior  as cantidad from wc_detalleinventario where idinventario='" . $idinventario . "' and estado=1 and idproducto='" . $idproducto . "';";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function cnProductosdevoluciones($idproducto, $fecha) {
        $sql = "select sum(det.cantidad) as cantidad from
                wc_detallemovimiento det
                Inner Join wc_movimiento mov On det.idmovimiento=mov.idmovimiento
                and mov.tipomovimiento=1 and mov.conceptomovimiento=3
                and iddevolucion!=0 and mov.estado=1 and det.estado=1 
                and mov.fechamovimiento>='" . $fecha . "' and det.idproducto='" . $idproducto . "';";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function cnProductosSalidas($idproducto, $fecha) {
        $sql = "select sum(det.cantidad) as cantidad from
                wc_detallemovimiento det
                Inner Join wc_movimiento mov On det.idmovimiento=mov.idmovimiento
                and mov.tipomovimiento=2 and mov.conceptomovimiento=1
                and iddevolucion=0 and mov.estado=1  and det.estado=1 
                and mov.fechamovimiento>='" . $fecha . "' and det.idproducto='" . $idproducto . "';";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function modificaStockProducto($idproducto, $stockactual) {

        $data['stockactual'] = $stockactual;
        $data['stockdisponible'] = $stockactual;
        $data = $this->actualizaRegistro("wc_producto_17001", $data, 'idproducto="' . $idproducto . '" and estado=1');
        return $data;
    }

    function ultimabloqueasignadodelproducto($idproducto) {
        $sql = "select bloque.codigo 
                        from wc_detalleinventario di
                        inner join wc_bloques bloque on bloque.idbloque = di.idbloque
                        where di.idproducto='$idproducto' and di.estado=1 and di.idbloque!=70
                        order by di.iddetalleinventario desc
                        limit 1;";
        $data = $this->EjecutaConsulta($sql);
        if (count($data) > 0) {
            return $data[0]['codigo'];
        }
        return '-';
    }

}

?>