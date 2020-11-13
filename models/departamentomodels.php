<?php

class Departamento extends Applicationbase {

    private $tabla = "wc_departamento";

    function grabar($data) {
        $this->grabaRegistro($this->tabla, $data);
    }

    function listado() {
        $data = $this->leeRegistro($this->tabla, "", "", "", "");
        return $data;
    }

    function buscarxid($id) {
        $data = $this->leeRegistro($this->tabla, "", "iddepartamento=" . $id, "", "");
        return $data;
    }

    function actualizar($id, $data) {
        $this->actualizaRegistro($this->tabla, $data, "id=" . $id);
    }

    function eliminar($id) {
        $this->eliminaRegistro($this->tabla, "id=" . $id);
    }

    function listadoOptionsdepartamento($codpais) {
        $data = $this->leeRegistro($this->tabla, "", "codigopais='" . $codpais . "'", "");
        return $data;
    }

}

?>