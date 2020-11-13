<?php

Class Atencioncliente extends Applicationbase {
    
    private $tabla3 = "`wc_recepcion` wc_recepcion inner join `wc_motivorecojo` wc_motivorecojo on wc_motivorecojo.`idmotivorecojo`=wc_recepcion.`tipomotivo` inner join `wc_cliente` wc_cliente on wc_cliente.`idcliente`=wc_recepcion.`idcliente` ";
    
    function grabaRecepcion($data) {
        $exito = $this->grabaRegistro("wc_recepcion", $data);
        return $exito;
    }
    
    function actualizaRecepcion($data, $idrecepcion, $filtro = "") {
        $exito=$this->actualizaRegistro("wc_recepcion", $data, "idrecepcion=$idrecepcion" . $filtro);
	return $exito;
    }
    
    function actualizaDetalleRecepcion($data, $iddetallerecepcion) {
        $exito=$this->actualizaRegistro("wc_detallerecepcion", $data, "iddetallerecepcion=$iddetallerecepcion");
	return $exito;
    }
    
    function EliminaraDetallesRecepcion($idrecepcion) {
        $data['estado'] = 0;
        $exito=$this->actualizaRegistro("wc_detallerecepcion", $data, "idrecepcion=$idrecepcion");
	return $exito;
    }
    
    function buscaDetalleRecepcion($idrecepcion, $iddetalleordenventa) {
        $data = $this->leeRegistro("wc_detallerecepcion", "*", "idrecepcion='$idrecepcion' and iddetalleordenventa='$iddetalleordenventa'", "");
        return $data;
    }
    
    function grabaDetalleRecepcion($data) {
        $exito = $this->grabaRegistro("wc_detallerecepcion", $data);
        return $exito;
    }
    
    function verRecepcionXid($idrecepcion, $filtro = "") {
        $data = $this->leeRegistro("wc_recepcion recepcion "
                . "left join wc_motivorecojo motivorecojo on motivorecojo.idmotivorecojo = recepcion.tipomotivo", 
                "recepcion.*, motivorecojo.nombre as nombremotivo", "recepcion.idrecepcion='$idrecepcion' and recepcion.estado=1" . $filtro, "");
        return $data;
    }
    
    function listaRecepcion($filtro = "") {
        $data = $this->leeRegistro("wc_recepcion recepcion "
                . "left join wc_motivorecojo motivorecojo on motivorecojo.idmotivorecojo = recepcion.tipomotivo", 
                "recepcion.*, motivorecojo.nombre as nombremotivo", "recepcion.estado=1" . $filtro, "recepcion.idrecepcion desc");
        return $data;
    }
            
    function listaDetalleRecepcion($idrecepcion) {
        $data = $this->leeRegistro("wc_detallerecepcion", "*", "idrecepcion='$idrecepcion' and estado=1", "");
        return $data;
    }
    
    function buscarAutocompletecxodigost($tex) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro("wc_recepcion", "idrecepcion, codigost", "estado=1 and codigost!='' and codigost LIKE '%$tex%'", "", "group by idrecepcion limit 0,10");
        foreach ($datos as $valor) {
            $dato[] = array("value" => $valor['codigost'],
                "label" => $valor['codigost'],
                "id" => $valor['idrecepcion']);
        }
        return $dato;
    }

    function autocompleteproductoxcliente($tex, $idcliente) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro("wc_ordenventa ordenventa
                                    inner join wc_detalleordenventa dov on dov.idordenventa=ordenventa.idordenventa and dov.estado=1 and cantdespacho>cantdevuelta
                                    inner join wc_producto producto on producto.idproducto=dov.idproducto", "producto.idproducto, concat(producto.codigopa, ' // ', producto.nompro) as nombrproducto", "ordenventa.vbcreditos=1 and ordenventa.esguiado=1 and ordenventa.estado=1 and ordenventa.idcliente='$idcliente' and (producto.codigopa LIKE '%$tex%' or producto.nompro LIKE '%$tex%')", "", "group by producto.idproducto limit 0,10");
        foreach ($datos as $valor) {
            $dato[] = array("value" => (html_entity_decode($valor['nombrproducto'], ENT_QUOTES, 'UTF-8')),
                "label" => (html_entity_decode($valor['nombrproducto'], ENT_QUOTES, 'UTF-8')),
                "id" => $valor['idproducto']);
        }
        return $dato;
    }

    function listaproductoxcliente($idproducto, $idcliente) {
        $datos = $this->leeRegistro("wc_ordenventa ordenventa
                                    inner join wc_actor vendedor on vendedor.idactor = ordenventa.idvendedor
                                    inner join wc_detalleordenventa dov on dov.idordenventa=ordenventa.idordenventa and dov.estado=1 and cantdespacho>cantdevuelta and dov.idproducto='$idproducto'
                                    inner join wc_producto producto on producto.idproducto=dov.idproducto", "dov.iddetalleordenventa, producto.codigopa, producto.nompro, ordenventa.codigov, ordenventa.fordenventa, ordenventa.IdMoneda, dov.preciofinal, (dov.cantdespacho-dov.cantdevuelta) as cantidad, concat(vendedor.apellidopaterno,' ',vendedor.apellidomaterno,' ',vendedor.nombres) as nombrevendedor", "ordenventa.vbcreditos=1 and ordenventa.esguiado=1 and ordenventa.estado=1 and ordenventa.idcliente='$idcliente'", "");
        return $datos;
    }

    function productoxDetalleordenventa($iddetalleordenventa, $idcliente) {
        $datos = $this->leeRegistro("wc_ordenventa ordenventa
                                    inner join wc_actor vendedor on vendedor.idactor = ordenventa.idvendedor
                                    inner join wc_detalleordenventa dov on dov.idordenventa=ordenventa.idordenventa and dov.estado=1 and cantdespacho>cantdevuelta and dov.iddetalleordenventa='$iddetalleordenventa'
                                    inner join wc_producto producto on producto.idproducto=dov.idproducto", "dov.iddetalleordenventa, producto.codigopa, producto.nompro, ordenventa.codigov, ordenventa.fordenventa, ordenventa.IdMoneda, dov.preciofinal, (dov.cantdespacho-dov.cantdevuelta) as cantidad, concat(vendedor.apellidopaterno,' ',vendedor.apellidomaterno,' ',vendedor.nombres) as nombrevendedor", "ordenventa.vbcreditos=1 and ordenventa.esguiado=1 and ordenventa.estado=1 and ordenventa.idcliente='$idcliente'", "");
        return $datos;
    }
    
    function generaCodigoST() {
        $data = $this->leeRegistro("wc_recepcion", "CONCAT( 'ST-',DATE_FORMAT( NOW( ) ,  '%y' ) , LPAD(  (MAX(SUBSTRING(`codigost`,6,6))+1) , 4,  '0' ) ) as codigo", "year(`fechacreacion`)=year(now())", "");
        if ($data[0]['codigo'] != "") {
            return strtoupper($data[0]['codigo']);
        } else {
            return "ST-" . date('y') . str_pad(1, 4, '0', STR_PAD_LEFT);
        }
    }
    
    function listaRecojosPaginado($pagina, $paraBusqueda = "") {
        $condicion = "wc_recepcion.`estado`=1 and wc_recepcion.`aprobado`=1 ";

        if (!empty($paraBusqueda)) {
            $condicion .= " and (wc_recepcion.`codigost` like '%$paraBusqueda%' or wc_cliente.`razonsocial` like '%$paraBusqueda%' or wc_cliente.`ruc` like '%$paraBusqueda%')";
        }

        $data = $this->leeRegistroPaginado(
                $this->tabla3, "wc_recepcion.*, wc_motivorecojo.nombre as nombremotivo, wc_cliente.razonsocial, wc_cliente.ruc, wc_cliente.dni", $condicion, "wc_recepcion.`codigost` desc", $pagina);
        return $data;
    }
    
    function paginadoRecojos($paraBusqueda = "") {
        $condicion = "wc_recepcion.`estado`=1 and wc_recepcion.`aprobado`=1 ";

        if (!empty($paraBusqueda)) {
            $condicion .= " and (wc_recepcion.`codigost` like '%$paraBusqueda%' or wc_cliente.`razonsocial` like '%$paraBusqueda%' or wc_cliente.`ruc` like '%$paraBusqueda%')";
        }

        return $this->paginado(
                        $this->tabla3, $condicion);
    }
    
    function cuentaRecojos($paraBusqueda = "") {
        $condicion = "wc_recepcion.`estado`=1 and wc_recepcion.`aprobado`=1 ";

        if (!empty($paraBusqueda)) {
            $condicion .= " and (wc_recepcion.`codigost` like '%$paraBusqueda%' or wc_cliente.`razonsocial` like '%$paraBusqueda%' or wc_cliente.`ruc` like '%$paraBusqueda%')";
        }
        $data = $this->leeRegistro($this->tabla3, "count(*)", $condicion, "");
        return $data[0]['count(*)'];
    }
    
    function ultimaDescarga() {
        $data = $this->leeRegistro("wc_detallerecepcion", "descargado", "estado=1", "descargado*1 desc limit 1");
        return ($data[0]['descargado'] + 1);
    }
    
    function listadoRecepciones($fechainicio, $fechafin, $idcliente, $tipo) {
        $condicion = '';
        if (!empty($fechainicio)) {
            $condicion .= " and recepcion.fremision>='$fechainicio'";
        }
        if (!empty($fechafin)) {
            $condicion .= " and recepcion.fremision<='$fechafin'";
        }
        if (!empty($idcliente)) {
            $condicion .= " and cliente.idcliente='$idcliente'";
        }
        if ($tipo == 1) {
            $condicion .= " and drecepcion.descargado=0";
        } else if ($tipo == 2) {
            $condicion .= " and drecepcion.descargado > 0";
        }
        $data = $this->leeRegistro("wc_detallerecepcion drecepcion " .
                "inner join wc_recepcion recepcion on recepcion.idrecepcion = drecepcion.idrecepcion and recepcion.aprobado=1 and recepcion.estado=1 " .
                "inner join wc_motivorecojo mrecojo on mrecojo.idmotivorecojo=recepcion.tipomotivo " .
                "inner join wc_cliente cliente on cliente.idcliente = recepcion.idcliente " .
                "inner join wc_detalleordenventa dov on dov.iddetalleordenventa = drecepcion.iddetalleordenventa " .
                "inner join wc_ordenventa ov on ov.idordenventa = dov.idordenventa " .
                "inner join wc_producto producto on producto.idproducto=dov.idproducto", 
                "(case when cliente.razonsocial is null then concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) else cliente.razonsocial end) as razonsocial, " .
                "cliente.ruc, " .
                "cliente.dni, " .
                "recepcion.idrecepcion, " .
                "recepcion.numero, " .
                "ov.codigov, " .
                "producto.codigopa, " .
                "producto.nompro, " .
                "recepcion.codigost, " .
                "recepcion.fremision, " .
                "recepcion.tipomotivo, " .
                "recepcion.prioridad, " .
                "mrecojo.nombre as nombremotivo, " .
                "drecepcion.*", "drecepcion.estado=1 " . $condicion, "recepcion.codigost asc");
        return $data;
    }

}

?>