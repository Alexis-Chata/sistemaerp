<?php

class Numerounico extends Applicationbase {

    private $tabla = "wc_numerounico";

    function listarNumerounico() {
        $data = $this->leeRegistro($this->tabla, "", "estado=1", "");
        return $data;
    }

    function listarNumerounicoCompracion() {
        $data = $this->leeRegistro($this->tabla, "", "comparacion=1 and estado=1", "");
        return $data;
    }
    
    function buscaNumerounico($idnumerounico) {
        $data = $this->leeRegistro($this->tabla, "", "idnumerounico=$idnumerounico", "");
        return $data;
    }

}

?>