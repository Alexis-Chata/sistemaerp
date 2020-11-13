<?php

class Cuadroutilidad extends Applicationbase {

    private $tabla = 'wc_cuadroutilidad';

    function graba($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function actualiza($data, $idcuadroutilidad) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idcuadroutilidad=$idcuadroutilidad");
        return $exito;
    }

}

?>