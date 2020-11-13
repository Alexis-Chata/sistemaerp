<?php

Class Descuento extends ApplicationBase {

    private $_name = "wc_descuentos";

    function listado() {
        return $this->leeRegistro($this->_name, "id,valor,dunico", "estado=1", "dunico", "");
    }

    function listadoTotal() {
        return $this->leeRegistro($this->_name, "id,valor,dunico", "", "dunico", "");
    }

    function buscarxid($id) {
        return $this->leeRegistro($this->_name, "id,dunico,valor", "id=" . $id, "", "");
    }

    function graba($data) {
        $exito = $this->grabaRegistro($this->_name, $data);
        return $exito;
    }

    function actualiza($data, $id) {
        $exito = $this->actualizaRegistro($this->_name, $data, "id=$id");
        return $exito;
    }

    function lista() {
        $data = $this->leeRegistro($this->_name, "", "estado=1", "");
        return $data;
    }

    function listaPaginado($pagina) {
        $data = $this->leeRegistroPaginado($this->_name, "", "", "", $pagina);
        return $data;
    }

    function buscarDescuento($valor, $dunico) {
        return $this->leeRegistro($this->_name, "*", "valor='" . $valor . "' and dunico='" . $dunico . "' and estado = 1", "", "");
    }

}

?>