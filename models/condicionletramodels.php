<?php

Class CondicionLetra extends Applicationbase {

    private $tabla = "wc_condicionletra";

    function buscaxId($id) {
        $data = $this->leeRegistro($this->tabla, "idcondicionletra,nombreletra,cantidadletra", "idcondicionletra=$id", "");
        return $data;
    }

    function grabaCondicionLetra($data) {
        return $this->grabaRegistro($this->tabla, $data);
    }

}

?>