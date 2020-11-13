<?php

Class Detallerecepcion extends Applicationbase {

    private $tabla = "wc_detallerecepcion";
    
    function verDxDetalleRecepcion($iddetallerecepcion) {
        $data = $this->leeRegistro("wc_detallerecepcion dr " .
                                    "inner join wc_recepcion recepcion on recepcion.idrecepcion = dr.idrecepcion " .
                                    "inner join wc_motivorecojo mrecojo on mrecojo.idmotivorecojo = recepcion.tipomotivo " .
                                    "inner join wc_detalleordenventa dov on dov.iddetalleordenventa = dr.iddetalleordenventa " .
                                    "inner join wc_producto producto on producto.idproducto = dov.idproducto " .
                                    "inner join wc_ordenventa ov on ov.idordenventa = dov.idordenventa " .
                                    "inner join wc_actor vendedor on vendedor.idactor = ov.idvendedor " .
                                    "inner join wc_cliente cliente on cliente.idcliente = ov.idcliente", 
                                    "dr.*, " .
                                    "producto.codigopa, " .
                                    "producto.nompro, " .
                                    "ov.codigov, " .      
                                    "mrecojo.nombre as nombremotivo, " .
                                    "cliente.idcliente, " . 
                                    "cliente.codcliente, " . 
                                    "cliente.razonsocial, " . 
                                    "cliente.nombrecli, " .
                                    "cliente.apellido1, " .
                                    "cliente.apellido2, " .
                                    "cliente.ruc, " .
                                    "cliente.dni, " .
                                    "concat(cliente.telefono, ' - ', cliente.celular) as celular, " . 
                                    "recepcion.codigost, " .
                                    "recepcion.fremision, " .
                                    "recepcion.numero, " .
                                    "concat(vendedor.apellidopaterno,' ',vendedor.apellidomaterno,' ',vendedor.nombres) as nombrevendedor", 
                                    "dr.iddetallerecepcion='$iddetallerecepcion' and dr.estado=1", "");
        $data[0]['razonsocial'] = ($data[0]['razonsocial'] != '' ? html_entity_decode($data[0]['razonsocial'], ENT_QUOTES, 'UTF-8') : html_entity_decode($data[0]['nombrecli'], ENT_QUOTES, 'UTF-8') . " " . html_entity_decode($data[0]['apellido1'], ENT_QUOTES, 'UTF-8') . " " . html_entity_decode($data[0]['apellido2'], ENT_QUOTES, 'UTF-8'));
        $data[0]['rucdni'] = ($data[0]['razonsocial'] != '' ? $data[0]['ruc'] : $data[0]['dni']);
        return $data;
    }
    
    function listadoFinalizados($pagina, $txtBusqueda = "") {
        $filtro = "";
        if (!empty($txtBusqueda)) {
            $filtro = "(recepcion.codigost like '%$txtBusqueda%' or " .
                       "cliente.razonsocial like '%$txtBusqueda%' or " .
                        "producto.nompro like '%$txtBusqueda%' or " .
                        "producto.codigopa like '%$txtBusqueda%') and ";
        }
        $data = $this->leeRegistroPaginado("wc_detallerecepcion drecepcion " .
                                                "inner join wc_recepcion recepcion on recepcion.idrecepcion = drecepcion.idrecepcion and recepcion.aprobado=1 and recepcion.estado=1 " .
                                                "inner join wc_motivorecojo mrecojo on mrecojo.idmotivorecojo=recepcion.tipomotivo " .
                                                "inner join wc_cliente cliente on cliente.idcliente = recepcion.idcliente " .
                                                "inner join wc_detalleordenventa dov on dov.iddetalleordenventa = drecepcion.iddetalleordenventa " .
                                                "inner join wc_producto producto on producto.idproducto=dov.idproducto", 
                                                "(case when cliente.razonsocial is null then concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) else cliente.razonsocial end) as razonsocial, " .
                                                "recepcion.idrecepcion, " .
                                                "recepcion.numero, " .
                                                "producto.codigopa, " .
                                                "producto.nompro, " .
                                                "recepcion.codigost, " .
                                                "recepcion.fremision, " .
                                                "recepcion.tipomotivo, " .
                                                "recepcion.prioridad, " .
                                                "mrecojo.nombre as nombremotivo, " .
                                                "drecepcion.*", 
                                                $filtro . "drecepcion.estado=1 and "
                                                . "drecepcion.separado=1 and "
                                                . "drecepcion.finalizado=1 and "
                                                . "drecepcion.ffinalizado!='' and "
                                                . "drecepcion.cantseparado=0 and "
                                                . "drecepcion.cantidad=(drecepcion.cantreparado + drecepcion.cantdescartado)", "recepcion.fremision, recepcion.prioridad desc", $pagina);
        return $data;
    }
    
    function listadoFinalizadosPaginado($txtBusqueda = "") {
        $filtro = "";
        if (!empty($txtBusqueda)) {
            $filtro = "(recepcion.codigost like '%$txtBusqueda%' or " .
                       "cliente.razonsocial like '%$txtBusqueda%' or " .
                        "producto.nompro like '%$txtBusqueda%' or " .
                        "producto.codigopa like '%$txtBusqueda%') and ";
        }
        return $this->paginado("wc_detallerecepcion drecepcion " .
                                    "inner join wc_recepcion recepcion on recepcion.idrecepcion = drecepcion.idrecepcion and recepcion.aprobado=1 and recepcion.estado=1 " .
                                    "inner join wc_motivorecojo mrecojo on mrecojo.idmotivorecojo=recepcion.tipomotivo " .
                                    "inner join wc_cliente cliente on cliente.idcliente = recepcion.idcliente " .
                                    "inner join wc_detalleordenventa dov on dov.iddetalleordenventa = drecepcion.iddetalleordenventa " .
                                    "inner join wc_producto producto on producto.idproducto=dov.idproducto",                  
                                    $filtro . "drecepcion.estado=1 and "
                                    . "drecepcion.separado=1 and "
                                    . "drecepcion.finalizado=1 and "
                                    . "drecepcion.ffinalizado!='' and "
                                    . "drecepcion.cantseparado=0 and "
                                    . "drecepcion.cantidad=(drecepcion.cantreparado + drecepcion.cantdescartado)");
    }
            
    function listadoAtendidos($pagina, $txtBusqueda = "") {
        $filtro = "";
        if (!empty($txtBusqueda)) {
            $filtro = "(recepcion.codigost like '%$txtBusqueda%' or " .
                       "cliente.razonsocial like '%$txtBusqueda%' or " .
                        "producto.nompro like '%$txtBusqueda%' or " .
                        "producto.codigopa like '%$txtBusqueda%') and ";
        }
        $data = $this->leeRegistroPaginado("wc_detallerecepcion drecepcion " .
                                            "inner join wc_recepcion recepcion on recepcion.idrecepcion = drecepcion.idrecepcion and recepcion.aprobado=1 and recepcion.estado=1 " .
                                            "inner join wc_motivorecojo mrecojo on mrecojo.idmotivorecojo=recepcion.tipomotivo " .
                                            "inner join wc_cliente cliente on cliente.idcliente = recepcion.idcliente " .
                                            "inner join wc_detalleordenventa dov on dov.iddetalleordenventa = drecepcion.iddetalleordenventa " .
                                            "inner join wc_producto producto on producto.idproducto=dov.idproducto", 
                                            "(case when cliente.razonsocial is null then concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) else cliente.razonsocial end) as razonsocial, " .
                                            "recepcion.idrecepcion, " .
                                            "recepcion.numero, " .
                                            "producto.codigopa, " .
                                            "producto.nompro, " .
                                            "recepcion.codigost, " .
                                            "recepcion.fremision, " .
                                            "recepcion.tipomotivo, " .
                                            "recepcion.prioridad, " .
                                            "mrecojo.nombre as nombremotivo, " .
                                            "drecepcion.*", 
                                            $filtro . "drecepcion.estado=1 and "
                                            . "drecepcion.separado=1 and "
                                            . "drecepcion.finalizado=0 and "
                                            . "drecepcion.cantseparado>0 and "
                                            . "drecepcion.cantidad=(drecepcion.cantreparado + drecepcion.cantdescartado + drecepcion.cantseparado)", "recepcion.fremision, recepcion.prioridad desc", $pagina);
        return $data;
    }
    
    function listadoAtendidosPaginado($txtBusqueda = "") {
        $filtro = "";
        if (!empty($txtBusqueda)) {
            $filtro = "(recepcion.codigost like '%$txtBusqueda%' or " .
                       "cliente.razonsocial like '%$txtBusqueda%' or " .
                        "producto.nompro like '%$txtBusqueda%' or " .
                        "producto.codigopa like '%$txtBusqueda%') and ";
        }
        return $this->paginado("wc_detallerecepcion drecepcion " .
                                "inner join wc_recepcion recepcion on recepcion.idrecepcion = drecepcion.idrecepcion and recepcion.aprobado=1 and recepcion.estado=1 " .
                                "inner join wc_motivorecojo mrecojo on mrecojo.idmotivorecojo=recepcion.tipomotivo " .
                                "inner join wc_cliente cliente on cliente.idcliente = recepcion.idcliente " .
                                "inner join wc_detalleordenventa dov on dov.iddetalleordenventa = drecepcion.iddetalleordenventa " .
                                "inner join wc_producto producto on producto.idproducto=dov.idproducto", 
                                $filtro . "drecepcion.estado=1 and "
                                . "drecepcion.separado=1 and "
                                . "drecepcion.finalizado=0 and "
                                . "drecepcion.cantseparado>0 and "
                                . "drecepcion.cantidad=(drecepcion.cantreparado + drecepcion.cantdescartado + drecepcion.cantseparado)");
    }

    function listadoPendientes() {
        $data = $this->leeRegistro("wc_detallerecepcion drecepcion " .
                                    "inner join wc_recepcion recepcion on recepcion.idrecepcion = drecepcion.idrecepcion and recepcion.aprobado=1 and recepcion.estado=1 " .
                                    "inner join wc_motivorecojo mrecojo on mrecojo.idmotivorecojo=recepcion.tipomotivo " .
                                    "inner join wc_cliente cliente on cliente.idcliente = recepcion.idcliente " .
                                    "inner join wc_detalleordenventa dov on dov.iddetalleordenventa = drecepcion.iddetalleordenventa " .
                                    "inner join wc_producto producto on producto.idproducto=dov.idproducto", "(case when cliente.razonsocial is null then concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) else cliente.razonsocial end) as razonsocial, " .
                                    "recepcion.idrecepcion, " .
                                    "recepcion.numero, " .
                                    "producto.codigopa, " .
                                    "producto.nompro, " .
                                    "recepcion.codigost, " .
                                    "recepcion.fremision, " .
                                    "recepcion.tipomotivo, " .
                                    "recepcion.prioridad, " .
                                    "mrecojo.nombre as nombremotivo, " .
                                    "drecepcion.*", "drecepcion.estado=1 and drecepcion.finalizado=0 and drecepcion.cantidad>(drecepcion.cantreparado + drecepcion.cantseparado)", "recepcion.fremision, recepcion.prioridad desc");
        return $data;
    }
    
    function actualiza($data, $filtro){
            $exito=$this->actualizaRegistro($this->tabla, $data, $filtro);
            return $exito;
    }

    function buscar($iddetallerecepcion) {
        $data = $this->leeRegistro($this->tabla, "*", "estado=1 and iddetallerecepcion='$iddetallerecepcion'", "");
        return $data;
    }
    
    

}

?>