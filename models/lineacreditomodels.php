<?php

Class Lineacredito extends Applicationbase {

    private $tabla1 = "wc_lineacredito";

    function guardaLineacredito($data) {
        $exito = $this->grabaRegistro($this->tabla1, $data);
        return $exito;
    }
    
    function buscaLineacredito($idlineacredito) {
        $data = $this->leeRegistro($this->tabla1, "", "idlineacredito='$idlineacredito'", "");
        return $data;
    }
    
    function actualizaLineacredito($data, $idLineacredito){
        $exito=$this->actualizaRegistro($this->tabla1,$data,"idlineacredito=$idLineacredito");
        return $exito;
    }

    function historiallineacreditoXcliente($idcliente) {
        $data = $this->leeRegistro($this->tabla1, "*", "idcliente='$idcliente' and estado=1", "idlineacredito desc");
        return $data;
    }
    
    function ultimalineacreditoXcliente($idcliente) {
        $data = $this->leeRegistro($this->tabla1, "*", "idcliente='$idcliente' and estado=1", "", "order by idlineacredito desc limit 1");
        return $data;
    }

    function listadoLineacredito($txtFechaInicio, $txtFechaFin, $idpadre, $idcategoria, $idzona, $cmbCalificacion, $chkContado, $chkCredito, $chkLetra, $txtmaximo, $txtminimo, $idCliente, $soloActivo) {
        $condicion = '';
        $condicion .= !empty($idpadre) ? " and ct.idpadrec='$idpadre'" : "";
        $condicion .= !empty($idcategoria) ? " and ct.idcategoria='$idcategoria'" : "";
        $condicion .= !empty($idzona) ? " and cliente.zona='$idzona'" : "";
        $condicion .= !empty($txtFechaInicio) ? " and lineacredito.fregistro >= '$txtFechaInicio'" : "";
        $condicion .= !empty($txtFechaFin) ? " and lineacredito.fregistro <= '$txtFechaFin'" : "";
        $condicion .= !empty($cmbCalificacion) ? " and lineacredito.calificacion='$cmbCalificacion'" : "";
        $condicion .= $chkContado ? " and lineacredito.contado=1" : "";
        $condicion .= $chkCredito ? " and lineacredito.credito=1" : "";
        $condicion .= $chkLetra ? " and lineacredito.letras=1" : "";
        $condicion .= !empty($txtmaximo) ? " and lineacredito.lineacredito <= '$txtmaximo'" : "";
        $condicion .= !empty($txtminimo) ? " and lineacredito.lineacredito >= '$txtminimo'" : "";
        $condicion .= !empty($idCliente) ? " and lineacredito.idcliente='$idCliente' and cliente.idcliente='$idCliente'" : "";
        $condicion .= ' and cliente.estado = 1';
        $condicion .= $soloActivo ? " group by cliente.idcliente " : '';
        $data = $this->EjecutaConsulta("SELECT cliente.idcliente, cliente.razonsocial, cliente.ruc, z.idzona, z.nombrezona, lineacredito.*
                                                FROM wc_cliente cliente
                                                inner join wc_zona z on cliente.zona = z.idzona
                                                inner join wc_categoria ct on ct.idcategoria = z.idcategoria
                                                inner join 
                                                            (select *
                                                                    from wc_lineacredito
                                                                    where estado = 1 
                                                            order by idlineacredito desc) as lineacredito on lineacredito.idcliente=cliente.idcliente " .
                                                $condicion .
                                                " order by cliente.idcliente, lineacredito.fechacreacion desc;");
        return $data;
    }

}

?>