<?php

class documentoTipo extends Applicationbase {

    private $tabla = 'wc_documentotipo';

    function listadoDocumentoTipo() {
        $movimiento = $this->leeRegistro($this->tabla, "", "estado=1", "", "");
        return $movimiento;
    }

    function listado() {
        $data = $this->leeRegistro($this->tabla, "", "estado=1", "");
        return $data;
    }

    function buscaxId($iddocumentotipo) {
        $data = $this->leeRegistro($this->tabla, "", "iddocumentotipo='$iddocumentotipo' and estado=1", "");
        return $data;
    }

    function actualiza($data, $iddocumentotipo) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "iddocumentotipo=$iddocumentotipo");
        return $exito;
    }

    function cambiaEstado($iddocumentotipo) {
        $exito = $this->inactivaRegistro($this->tabla, "iddocumentotipo=$iddocumentotipo");
        return $exito;
    }

    function graba($data) {
        $estado = $this->grabaRegistro($this->tabla, $data);

        return $estado;
    }

}

?>