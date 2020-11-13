<?php

class Disenio extends Applicationbase{
    function listaProducto($idProducto) {
        $condicion = "p.estado=1";
        if (!empty($idProducto)) {
            $condicion = "p.idproducto=$idProducto";
        }
        $stockProducto = $this->leeRegistro("wc_producto p
        left join wc_empaque as e on p.empaque=e.idempaque
        left join wc_detalleordencompra as doc on doc.idproducto=p.idproducto
        left join wc_ordencompra as oc on doc.idordencompra=oc.idordencompra
        left join wc_actor as a on a.idactor=oc.jefelinea
        left join wc_unidadmedida as um on um.idunidadmedida=p.unidadmedida",
        "p.codigopa, p.nompro, um.codigo as unidm, oc.codigooc, oc.fordencompra, p.stockactual,p.stockdisponible, concat(a.nombres,' ',a.apellidopaterno, ' ', a.apellidomaterno) as responsable, e.codempaque ",
        "$condicion", "", "order by oc.idordencompra desc limit 1");
        return $stockProducto;
    }
}
