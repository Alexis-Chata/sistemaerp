<?php

class TipoOperacion extends Applicationbase {

    private $tabla = 'wc_tipooperacion';

    function listadoTipoOperacion($tipomovimiento) {
        $movimiento = $this->leeRegistro($this->tabla, "", "tipomovimiento='$tipomovimiento' ", "", "");
        return $movimiento;
    }

    function listadoTipoOperacionxId($idtipooperacion) {
        $movimiento = $this->leeRegistro($this->tabla, "", "idtipooperacion='$idtipooperacion'", "", "");
        return $movimiento;
    }

    function listado() {
        $data = $this->leeRegistro($this->tabla, "", "estado=1", "");
        return $data;
    }

    function buscaxId($idtipooperacion) {
        $data = $this->leeRegistro($this->tabla, "", "idtipooperacion='$idtipooperacion' and estado=1", "");
        return $data;
    }

    function actualiza($data, $idtipooperacion) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idtipooperacion=$idtipooperacion");
        return $exito;
    }

    function cambiaEstado($idtipooperacion) {
        $exito = $this->inactivaRegistro($this->tabla, "idtipooperacion=$idtipooperacion");
        return $exito;
    }

    function graba($data) {
        $estado = $this->grabaRegistro($this->tabla, $data);

        return $estado;
    }

}

?>