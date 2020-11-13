<?php

class Controlinternost extends Applicationbase {

    private $tabla = "wc_controlinternost";
    
    public function graba($data) {
        $exito=$this->grabaRegistro($this->tabla, $data);
	return $exito;
    }
    
    public function listadoxdetallederecepciontecnico($iddetallerecepciontecnico) {
        $data = $this->leeRegistro($this->tabla, "*", "iddetallerecepciontecnico='$iddetallerecepciontecnico' and estado=1", "");
        return $data;
    }
    
    public function listadoxFechaFin($fechafin) {
        $data = $this->leeRegistro($this->tabla . " cist " .
                                    "inner join wc_detallerecepciontecnico drt on drt.iddetallerecepciontecnico = cist.iddetallerecepciontecnico " .
                                    "inner join wc_detallerecepcion dr on dr.iddetallerecepcion = drt.iddetallerecepcion " .
                                    "inner join wc_detalleordenventa dov on dov.iddetalleordenventa = dr.iddetalleordenventa " .
                                    "inner join wc_producto producto on producto.idproducto = dov.idproducto " .
                                    "inner join wc_actor tecnico on tecnico.idactor = drt.idtecnico", 
                                    "cist.*, " .
                                    "producto.codigopa, " .
                                    "producto.nompro, " .
                                    "drt.idtecnico, " .
                                    "producto.codigopa, " . 
                                    "producto.nompro, " .
                                    "concat(tecnico.nombres, ' ', tecnico.apellidopaterno, ' ', tecnico.apellidomaterno) as nombretecnico", 
                                    "cist.ffin='$fechafin' and cist.estado=1", 
                                    "drt.idtecnico, cist.horafin, cist.minutofin desc");
        return $data;
    }
    
    public function bitacoradeActividades($txtFecha, $situacionDRT, $idRecepcion, $txtTecnico, $txtProducto, $SituacionCI) {
        $filtro = "cist.estado = 1";
        $filtro .= (!empty($txtFecha) ? ' and cist.finicio>="' . $txtFecha . '" and cist.ffin<="' . $txtFecha . '"' : '');
        $filtro .= (!empty($situacionDRT) ? ' and drt.situacion="' . $situacionDRT . '"' : '');
        $filtro .= (!empty($idRecepcion) ? ' and recepcion.idrecepcion="' . $idRecepcion . '"' : '');
        $filtro .= (!empty($txtTecnico) ? ' and drt.idtecnico="' . $txtTecnico . '"' : '');
        $filtro .= (!empty($txtProducto) ? ' and dov.idproducto="' . $txtProducto . '"' : '');
        $filtro .= (!empty($SituacionCI) ? ' and cist.situacion="' . $SituacionCI . '"' : '');
        $data = $this->leeRegistro($this->tabla . " cist " .
                                    "inner join wc_detallerecepciontecnico drt on drt.iddetallerecepciontecnico = cist.iddetallerecepciontecnico " .
                                    "inner join wc_actor actor on actor.idactor = drt.idtecnico " .
                                    "inner join wc_detallerecepcion dr on dr.iddetallerecepcion = drt.iddetallerecepcion " .
                                    "inner join wc_detalleordenventa dov on dov.iddetalleordenventa = dr.iddetalleordenventa " .
                                    "inner join wc_producto producto on producto.idproducto = dov.idproducto " .
                                    "inner join wc_recepcion recepcion on recepcion.idrecepcion = dr.idrecepcion", 
                                    "drt.idtecnico, " .
                                    "concat(actor.nombres, ' ', actor.apellidopaterno, ' ', actor.apellidomaterno) as tecnico, " .
                                    "producto.codigopa, " .
                                    "producto.nompro, " .
                                    "cist.cantidad, " .
                                    "cist.situacion, " .
                                    "cist.informe, " .
                                    "cist.finicio, " .
                                    "cist.horainicio, " .
                                    "cist.minutoinicio, " .
                                    "cist.ffin, " .
                                    "cist.horafin, " .
                                    "cist.minutofin, " .
                                    "drt.iddetallerecepciontecnico, " .
                                    "recepcion.codigost", 
                                    $filtro, "", 
                                    "group by cist.idcontrolinternost " .
                                    "order by drt.idtecnico, drt.iddetallerecepciontecnico, cist.finicio asc");
        return $data;
    }
    
    public function verControlInternoConDetalle($idcontrolinternost) {
        $data = $this->leeRegistro("wc_controlinternost cist " .
                                    "inner join wc_detallerecepciontecnico drt on drt.iddetallerecepciontecnico = cist.iddetallerecepciontecnico " .
                                    "inner join wc_detallerecepcion dr on dr.iddetallerecepcion = drt.iddetallerecepcion " .
                                    "inner join wc_actor tecnico on tecnico.idactor = drt.idtecnico " .
                                    "inner join wc_recepcion recepcion on recepcion.idrecepcion = dr.idrecepcion " .
                                    "inner join wc_motivorecojo mrecojo on mrecojo.idmotivorecojo = recepcion.tipomotivo " .
                                    "inner join wc_detalleordenventa dov on dov.iddetalleordenventa = dr.iddetalleordenventa " .
                                    "inner join wc_producto producto on producto.idproducto = dov.idproducto " .
                                    "inner join wc_ordenventa ov on ov.idordenventa = dov.idordenventa " .
                                    "inner join wc_actor vendedor on vendedor.idactor = ov.idvendedor " .
                                    "inner join wc_actor recogido on recogido.idactor = recepcion.idrecogido " .
                                    "inner join wc_cliente cliente on cliente.idcliente = recepcion.idcliente " .
                                    "inner join wc_distrito distrito on cliente.iddistrito = distrito.iddistrito " .
                                    "inner join wc_provincia provincia on provincia.idprovincia = distrito.idprovincia " .
                                    "inner join wc_departamento departamento on provincia.iddepartamento = departamento.iddepartamento", 
                                    "cist.*, " .
                                    "(case when " . 
                                    "        cliente.razonsocial is null then " .
                                    "        concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) " .
                                    "else " .
                                    "        cliente.razonsocial end) as razonsocial, " .
                                    "cliente.direccion, " .
                                    "distrito.nombredistrito, " .
                                    "provincia.nombreprovincia, " .
                                    "departamento.nombredepartamento, " . 
                                    "recepcion.fremision, " .
                                    "recepcion.fregistro, " .
                                    "dr.observaciones, " .
                                    "dr.garantia as drgarantia, " .
                                    "concat(tecnico.nombres, ' ', tecnico.apellidopaterno, ' ', tecnico.apellidomaterno) as nombretecnico, " .
                                    "concat(recogido.nombres, ' ', recogido.apellidopaterno, ' ', recogido.apellidomaterno) as nombrerecogido, " .
                                    "ov.codigov, " .
                                    "ov.fordenventa, " .
                                    "concat(vendedor.nombres, ' ', vendedor.apellidopaterno, ' ', vendedor.apellidomaterno) as nombrevendedor, " .
                                    "recepcion.tipomotivo, " .
                                    "mrecojo.nombre as nombremotivo, " .
                                    "producto.codigopa, " .
                                    "producto.nompro", 
                                    "cist.idcontrolinternost='$idcontrolinternost' and cist.estado=1", "");
        return $data;
    }

}

?>