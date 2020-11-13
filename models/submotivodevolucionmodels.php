<?php

class Submotivodevolucion extends Applicationbase {

    private $tabla = "wc_submotivodevolucion";

    function listado() {
        $data = $this->leeRegistro($this->tabla, "", "estado=1", "");
        return $data;
    }

    function grabar($data) {
        $estado = $this->grabaRegistro($this->tabla, $data);
        return $estado;
    }

    function actualiza($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    function buscar($idsubmotivodevolucion) {
        $data = $this->leeRegistro($this->tabla, "", "idsubmotivodevolucion=$idsubmotivodevolucion", "");
        return $data;
    }

    function cambiaEstado($idsubmotivodevolucion) {
        $exito = $this->cambiaEstado($this->tabla, "idsubmotivodevolucion=$idsubmotivodevolucion");
        return $exito;
    }

    function leerPorTipo($tipo) {
        $data = $this->leeRegistro($this->tabla, "", "tipo=$tipo", "");
        return $data;
    }

}

?>