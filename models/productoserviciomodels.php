<?php

class Productoservicio extends Applicationbase {

    private $tabla = "wc_productoservicio";

    function listadoProductoservicio() {
        return $this->leeRegistro($this->tabla, "", "estado=1", "", "");
    }

    function verificarProductoservicio($nombre) {
        return $this->leeRegistro($this->tabla, "idproductoservicio", "estado=1 and nombre='$nombre'", "", "");
    }

    function grabar($data) {
        return $this->grabaRegistro($this->tabla, $data);
    }

}

?>