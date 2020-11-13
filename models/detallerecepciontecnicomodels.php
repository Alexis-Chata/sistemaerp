<?php

Class Detallerecepciontecnico extends Applicationbase {

    private $tabla = "wc_detallerecepciontecnico";

    function graba($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }
    
    function actualiza($data, $filtro){
            $exito=$this->actualizaRegistro($this->tabla, $data, $filtro);
            return $exito;
    }
    
    function verDetallerecepciontecnico($iddetallerecepciontecnico) {
        $data = $this->leeRegistro($this->tabla . " drt " . 
                                    "inner join wc_actor actor on actor.idactor = drt.idtecnico " . 
                                    "inner join wc_detallerecepcion dr on dr.iddetallerecepcion = drt.iddetallerecepcion " . 
                                    "inner join wc_recepcion recepcion on recepcion.idrecepcion = dr.idrecepcion " . 
                                    "inner join wc_cliente cliente on cliente.idcliente = recepcion.idcliente " . 
                                    "inner join wc_detalleordenventa dov on dov.iddetalleordenventa = dr.iddetalleordenventa " . 
                                    "inner join wc_ordenventa ordenventa on ordenventa.idordenventa = dov.idordenventa " . 
                                    "inner join wc_producto producto on producto.idproducto = dov.idproducto", 
                                    "concat(actor.nombres, ' ', actor.apellidopaterno, ' ', actor.apellidopaterno) as tecnico, " .
                                    "recepcion.codigost, " .                                    
                                    "ordenventa.codigov, " .
                                    "(case when cliente.razonsocial is null then concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) else cliente.razonsocial end) as razonsocial, " .
                                    "(case when cliente.razonsocial is null then cliente.dni else cliente.ruc end) as ruc, " .
                                    "concat(cliente.telefono, ' - ', cliente.celular) as celular, " .
                                    "producto.codigopa, " .
                                    "producto.nompro, " .
                                    "drt.*, " .
                                    "dr.observaciones, " .
                                    "dr.garantia, " .
                                    "recepcion.prioridad", 
                                    "drt.estado=1 and drt.iddetallerecepciontecnico='$iddetallerecepciontecnico' and drt.estado=1", "");
        return $data;
    }
    
    function listadoxiddetallerecepcion($iddetallerecepcion) {
        $data = $this->leeRegistro($this->tabla . " drt " . 
                                    "inner join wc_actor actor on actor.idactor = drt.idtecnico", 
                                    "concat(actor.nombres, ' ', actor.apellidopaterno, ' ', actor.apellidomaterno) as tecnico, drt.* ", 
                                    "drt.estado=1 and drt.iddetallerecepcion='$iddetallerecepcion' and drt.estado=1", "");
        return $data;
    }
    
    function listadodetallerecepcionenproceso_tecnico($idtecnico) {
        $data = $this->leeRegistro($this->tabla . " drt" .
                                    " inner join wc_detallerecepcion drecepcion on drecepcion.iddetallerecepcion=drt.iddetallerecepcion and drecepcion.estado=1" .
                                    " inner join wc_recepcion recepcion on recepcion.idrecepcion=drecepcion.idrecepcion and recepcion.estado=1" .
                                    " inner join wc_cliente cliente on cliente.idcliente=recepcion.idcliente" .
                                    " inner join wc_detalleordenventa dov on dov.iddetalleordenventa=drecepcion.iddetalleordenventa" .
                                    " inner join wc_producto producto on producto.idproducto=dov.idproducto" .
                                    " inner join wc_ordenventa ordenventa on ordenventa.idordenventa=dov.idordenventa", 
                                    "drt.*, " .
                                    "recepcion.codigost, " .
                                    "drecepcion.garantia, " .
                                    "(case when cliente.razonsocial is null then concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) else cliente.razonsocial end) as razonsocial, " .
                                    "concat(cliente.telefono, ' - ', cliente.celular) as celular, " .
                                    "ordenventa.codigov, " .
                                    "producto.codigopa, " .
                                    "producto.nompro", 
                                    "drt.cantidad>drt.avance and drt.estado=1 and drt.idtecnico='$idtecnico'", "drt.fecha desc", "");
        return $data;
        
    }

}

?>