<?php

class OrdenPago extends Applicationbase {

    private $tabla = "wc_ordenpago";
    private $tablas = "wc_orden,wc_ordenpago,";

    function listado($idorden = "") {
        $filtro = ($idorden != "" ? ("idorden=" . $idorden . " and ") : "");
        $data = $this->leeRegistro($this->tabla, "", $filtro, "");
        return $data;
    }

    function grabaOrdenPago($data) {
        $data['estado'] = 1;
        $data['situacion'] = "pendiente";
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function contarOrdenPago() {
        $cantidadOrdenPago = $this->contarRegistro($this->tabla, "");
        return $cantidadOrdenPago;
    }

    function editaOrdenPago($id) {
        $data = $this->leeRegistro($this->tabla, "", "idordenpago=$id", "");
        return $data;
    }

    function eliminaOrdenPago($id) {
        $exito = $this->cambiaEstado($this->tabla, "idordenpago=$id");
        return $exito;
    }

    function actualizaOrdenPago($data, $idOrdenPago) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idordenpago=$idOrdenPago");
        return $exito;
    }

    function buscaOrdenPago($idOrdenPago) {
        $data = $this->leeRegistro($this->tabla, "", "idordenpago=$idOrdenPago", "");
        return $data;
    }

    function listarxguia($idguia) {
        $data = $this->leeRegistro($this->tabla, "idordenpago,importe,fvencimiento,tipo", "idorden=$idguia", "");
        return $data;
    }

    function tieneletras($idordenpago) {
        $r = $this->leeRegistro($this->tabla, "", "idordenpago=$idordenpago and tipo=3", "");
        if (count($r)) {
            return true;
        } else
            return false;
    }

}

?>