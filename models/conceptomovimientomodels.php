<?php

class Conceptomovimiento extends Applicationbase {

    private $tabla = "wc_conceptomovimiento";

    function listadoConceptosmovimiento() {
        $data = $this->leeRegistro($this->tabla, "", "", "");
        return $data;
    }

    function listadoOptionsConceptosMovimiento($idTipoMovimiento) {
        $data = $this->leeRegistro($this->tabla, "", "idtipomovimiento=" . $idTipoMovimiento, "");
        return $data;
    }

    function grabaConceptomovimiento($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function editaConceptoMovimiento($idConceptoMovimiento) {
        $data = $this->leeRegistro($this->tabla, "", "idconceptomovimiento=$idConceptoMovimiento", "");
        return $data;
    }

    function actualizaConceptoMovimiento($data, $idConceptoMovimiento) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idconceptomovimiento=$idConceptoMovimiento");
        return $exito;
    }

    function eliminaConceptoMoviento($idConceptoMovimiento) {
        $exito = $this->cambiaEstado($this->tabla, "idconceptomovimiento=$idConceptoMovimiento");
        return $exito;
    }

}

?>