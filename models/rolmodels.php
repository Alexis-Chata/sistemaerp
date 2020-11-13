<?php

Class Rol extends Applicationbase {

    private $tabla;

    function __construct() {
        $this->tabla = "wc_rol";
    }

    function RolCombo() {
        $data = $this->leeRegistro($this->tabla, "idrol,nombre", "", "", "");
        return $data;
    }

    public function listadoRol($inicio = 0, $tamanio) {
        $inicio = ($inicio - 1) * $tamanio;
        if ($inicio < 0) {
            $inicio = 0;
        }
        $data = $this->leeRegistro($this->tabla, "idrol,codigo,nombre,descripcion", "estado=1", "", "Limit " . $inicio . "," . $tamanio);
        return $data;
    }

    function ListadoRolesxId() {
        $a = $this->leeRegistro($this->tabla, "", "", "Estado desc", "");
        return $a;
    }

    public function buscaRolesxId($id) {
        $data = $this->leeRegistro($this->tabla, "", "idRol=" . $id, "", "");
        return $data;
    }

    public function cambiaestadoRol($id) {
        return $this->cambiaEstado($this->tabla, "idrol=" . $id);
    }

    function grabaRol($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function buscarxrol($rol) {
        $rol = $this->leeRegistro($this->tabla, "", "nombre like '%$rol%'", "", "");
        return $rol;
    }

    public function actualizaRol($data, $id) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idRol=" . $id);
        return $exito;
    }

    public function Paginacion($tamanio) {
        $data = $this->leeRegistro($this->tabla, "IdRol", "Estado=1", "", "");
        $paginas = intval((count($data) / $tamanio)) + 1;
        return $paginas;
    }

}

?>