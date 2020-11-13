<?php

class Letras extends Applicationbase {

    private $tabla = "wc_condicionletra";
    private $tablas = 'wc_orden o,wc_ordenpago op,wc_condicionletra l';

    function listado() {
        $cuenta = $this->leeRegistro($this->tabla, "", "estado=1", "nombreletra");
        return $cuenta;
    }

    function graba($data) {
        $id = $this->grabaRegistro($this->tabla, $data);
        return $id;
    }

    function actualiza($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    function buscar($id) {
        $data = $this->leeRegistro($this->tabla, "", "idletras=$id", "");
        return $data;
    }

    function cambiaEstado($idletra) {
        $exito = $this->inactivaRegistro($this->tabla, "idcondicionletra=$idletra");
        return $exito;
    }

    function listarxvendedor($idvendedor) {
        $data = $this->leeRegistro($this->tabla, "", " situacionpago=0 and idvendedor=" . $idvendedor, "");
        return $data;
    }

    function listarxzona($idzona) {
        $data = $this->leeRegistro($this->tabla, "", " estado=1 and idzona=" . $idzona, "");
        return $data;
    }

    function contarLetras() {
        $cantidad = $this->contarRegistro($this->tabla, "estado=1");
        return $cantidad;
    }

    function listarxcliente($idcliente) {
        $data = $this->leeRegistro($this->tabla, "", "idcliente=" . $idcliente, "");
        return $data;
    }

    function listarxordenpago($id) {
        $data = $this->leeRegistro($this->tabla, "", "idordenpago=" . $id, "");
        return $data;
    }

    function listarxguia($id) {
        $data = $this->leeRegistro($this->tablas, "l.codigoletra,o.idorden,op.idordenpago,l.fechaemision,
				l.condicionletra,l.tipoletra", "o.idorden=op.idordenpago and op.idorden=" . $id . " and l.idordenpago=op.idordenpago", "");
        return $data;
    }

}

?>
