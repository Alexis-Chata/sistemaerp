<?php

class Cargo extends Applicationbase {

    private $tabla = "wc_cargo";

    function listadoCargos() {
        return $this->leeRegistro($this->tabla, "", "estado=1", "", "");
    }

    function verificarCargo($nombre) {
        return $this->leeRegistro($this->tabla, "idcargo", "estado=1 and nombre='$nombre'", "", "");
    }

    function grabar($data) {
        return $this->grabaRegistro($this->tabla, $data);
    }

}

?>