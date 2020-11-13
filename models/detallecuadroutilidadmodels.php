<?php

class Detallecuadroutilidad extends Applicationbase {

    private $tabla = 'wc_detallecuadroutilidad';

    function graba($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function actualiza($data, $iddetallecuadroutilidad) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "iddetallecuadroutilidad=$iddetallecuadroutilidad");
        return $exito;
    }

    function listarXidcuadroutilidad($idcuadroutilidad, $idordencompra) {
        $sql = "Select detOC.piezas, detOC.carton, doc.*, doc.cantidad as cantidadrecibidaoc, doc.fobunitariodolares as fobdoc, p.codigopa, p.nompro,m.nombre as marca,um.nombre as unidadmedida
					From wc_detallecuadroutilidad doc
					Inner Join wc_producto p On doc.idproducto=p.idProducto
                                        Inner Join wc_detalleordencompra detOC on detOC.idproducto=p.idProducto and detOC.idordencompra='".$idordencompra."' and detOC.estado=1
					Left Join wc_marca m On p.idmarca=m.idmarca
					Left Join wc_unidadmedida um On um.idunidadmedida=p.unidadmedida
				 Where doc.estado=1 and doc.idcuadroutilidad=" . $idcuadroutilidad;
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

}

?>