<?php

class Evaluador extends Applicationbase {

    private $tabla = "wc_evaluador";

    function listadoEvaluadores() {
        return $this->leeRegistro($this->tabla, "", "estado=1", "", "");
    }

    function verificarEvaluador($nombre) {
        return $this->leeRegistro($this->tabla, "idevaluador", "estado=1 and nombre='$nombre'", "", "");
    }

    function grabar($data) {
        return $this->grabaRegistro($this->tabla, $data);
    }

}

?>