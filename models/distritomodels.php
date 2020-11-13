<?php

class Distrito extends Applicationbase {

    private $tabla = "wc_distrito";

    function listado($idProvincia) {
        $data = $this->leeRegistro($this->tabla, "", "idprovincia=$idProvincia", "", "");
        return $data;
    }

    function grabar($data) {
        $this->grabaRegistro($this->tabla, $data);
    }

    function buscarxid($id) {
        $data = $this->leeRegistro($this->tabla, "", "iddistrito=" . $id, "", "");
        return $data;
    }

    function actualizar($id, $data) {
        $this->actualizaRegistro($this->tabla, $data, "iddistrito=" . $id);
    }

    function eliminar($id) {
        $this->eliminaRegistro($this->tabla, "iddistrito=" . $id);
    }

    function listadoOptionsdistrito($idProvincia) {
        $data = $this->leeRegistro($this->tabla, "", "idprovincia=" . $idProvincia, "");
        return $data;
    }

}

?>