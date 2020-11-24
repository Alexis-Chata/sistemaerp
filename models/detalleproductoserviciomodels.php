<?php

class Detalleproductoservicio extends Applicationbase {

    private $tabla = "wc_detalleproductoservicio";

    function listadoDetalleproductoservicio() {
        return $this->leeRegistro($this->tabla, "", "estado=1", "", "");
    }

    function verificarDetalleproductoservicio($nombre) {
        return $this->leeRegistro($this->tabla, "iddetalleproductoservicio", "estado=1 and idproveedornacional = 0 and nombre='$nombre'", "", "");
    }

    function grabar($data) {
        return $this->grabaRegistro($this->tabla, $data);
    }

}

?>