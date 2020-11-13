<?php

class DetalleOrdenVenta extends Applicationbase {

    private $tabla = "wc_detalleordenventa";
    private $tabla2 = "wc_detalleordenventa,wc_ordenventa,wc_producto";

    function graba($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function actualizar($id, $data) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "iddetalleordenventa=$id");
        return $exito;
    }

    function actualizaxFiltro($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    function listaDetalleOrdenVenta($idOrdenVenta) {
        $data = $this->leeRegistro3($this->tabla2, "*,t1.`preciolista` as preciolista2", "t1.idordenventa=$idOrdenVenta and t1.estado=1 ", "", 2);
        return $data;
    }

    function verificarVentadelproducto($idproducto) {
        $data = $this->leeRegistro("wc_ordenventa ov " .
                "inner join wc_detalleordenventa dov on dov.idordenventa=ov.idordenventa and dov.idproducto='" . $idproducto . "' and dov.estado=1", "sum(dov.estado) as totalventas", "ov.codigov!='' and ov.vbalmacen=1 and ov.esdespachado=1 and ov.estado=1", "", "limit 1");
        return $data[0]['totalventas'];
    }

    function listaDetalleOrdenVentaGuia($idOrdenVenta) {
        $data = $this->leeRegistro("wc_detalleordenventa dov 
					inner join wc_ordenventa ov on dov.idordenventa=ov.idordenventa
					inner join wc_producto p on p.idproducto=dov.idproducto
					left join wc_unidadmedida u on u.idunidadmedida=p.unidadmedida
					", "*,p.unidadmedida as idunidaddemedida, p.preciolista as preciolista2,u.codigo as unidadmedida", "dov.idordenventa='$idOrdenVenta' and dov.estado=1 ", "");
        return $data;
    }

    function listaDetalle($idOV) {
        $condicion = "estado=1";
        if (!empty($idOV)) {
            $condicion = "estado=1 and idordenventa='" . $idOV . "'";
        }

        $data = $this->leeRegistro($this->tabla, "", $condicion, "");
        return $data;
    }

    function listaDetalleProductos($idOV) {
        $condicion = "dov.estado=1 and dov.idordenventa='$idOV'";
        $data = $this->leeRegistro("wc_detalleordenventa dov inner join wc_producto p on p.idproducto=dov.idproducto inner join wc_almacen a on a.idalmacen=p.idalmacen ", "*,dov.preciolista as preciolista2", $condicion, "");
        return $data;
    }

    function listaDetalleOrdenVentaxProducto($idordenventa, $idproducto) {
        $condicion = "idordenventa='$idordenventa' and idproducto='$idproducto' and estado=1";
        $data = $this->leeRegistro($this->tabla, "", $condicion, "", "");
        return $data;
    }

    function listaDetalleOrdenVentaYOrden($idordenventa) {

        $data = $this->leeRegistro("wc_ordenventa ov inner join wc_detalleordenventa dov on ov.idordenventa=dov.idordenventa", "", "ov.idordenventa='$idordenventa' and dov.estado=1", "", "");
        return $data;
    }

    function sumaCantidadProducto($filtro) {
        $data = $this->leeRegistro("wc_ordenventa as ov INNER JOIN wc_detalleordenventa as dov ON ov.idordenventa=dov.idordenventa", "sum(dov.cantsolicitada)", $filtro, "");
        $respuesta = empty($data[0]['sum(dov.cantsolicitada)']) ? 0 : $data[0]['sum(dov.cantsolicitada)'];
        return $respuesta;
    }

    function listaxFiltro($filtro) {
        $data = $this->leeRegistro("wc_detalleordenventa dov 
					inner join wc_ordenventa ov on dov.idordenventa=ov.idordenventa
					inner join wc_producto p on p.idproducto=dov.idproducto
					left join wc_descuentos d on dov.descuentoaprobado=d.id
					", "p.idproducto,p.codigopa,p.nompro,dov.preciofinal,dov.descuentoaprobado,d.dunico,dov.iddetalleordenventa,dov.precioaprobado,dov.preciolista", $filtro, "");
        return $data;
    }

    function importesProductoDeuda() {
        $sql = "select m.simbolo, ov.idordenventa, ov.importepagado, c.razonsocial, c.idcliente
                                    from wc_detalleordenventa dov
                            inner join wc_ordenventa ov on dov.idordenventa = ov.idordenventa 
                            inner join wc_cliente c on c.idcliente = ov.idcliente 
                            left join wc_moneda m on m.idmoneda = ov.idmoneda
                            where dov.idproducto = 1217 and (ov.idvendedor = 184 or ov.idvendedor = 264) and  ov.estado = 1 and dov.estado = 1
                            order by c.razonsocial asc";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

}

?>
