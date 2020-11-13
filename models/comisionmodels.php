<?php

class comision extends Applicationbase {

    private $tabla = "wc_comision";

    function actualizaComision($data, $idOrdenVenta) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idordenventa=$idOrdenVenta");
        return $exito;
    }

    function grabar($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function buscarxidOrdenVenta($idordenventa) {
        $data = $this->leeRegistro($this->tabla, "", "idordenventa='$idordenventa'", "");
        return $data;
    }

}

?>