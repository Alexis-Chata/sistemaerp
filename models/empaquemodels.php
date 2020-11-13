<?php

class Empaque extends Applicationbase {

    private $tabla = "wc_empaque";

    function listarEmpaque() {
        $data = $this->leeRegistro($this->tabla, "", "estado=1", "");
        return $data;
    }

    function guardaEmpaque($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function buscaEmpaque($idEmpaque) {
        $data = $this->leeRegistro($this->tabla, "", "idempaque=$idEmpaque", "");
        return $data;
    }

    function actualizaEmpaque($data, $idEmpaque) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idempaque=$idEmpaque");
        return $exito;
    }

    function eliminaEmpaque($idEmpaque) {
        $exito = $this->cambiaEstado($this->tabla, "idempaque=$idEmpaque");
        return $exito;
    }

}

?>