<?php

class Marca extends Applicationbase {

    private $tabla = "wc_marca";

    function listado() {
        $data = $this->leeRegistro($this->tabla, "idmarca,nombre", "estado=1", "");
        return $data;
    }

    function listaxId($id) {
        $data = $this->leeRegistro($this->tabla, "idmarca,nombre", "idmarca='$id' and estado=1", "");
        return $data;
    }

    function listaxNombre($parametro) {
        $parametro = htmlentities($parametro, ENT_QUOTES, 'UTF-8');
        $data = $this->leeRegistro($this->tabla, "idmarca,nombre", "nombre like '%$parametro%'  and estado=1", "");
        return $data;
    }

    function listaMarcaPaginado($pagina) {
        $data = $this->leeRegistroPaginado(
                $this->tabla, "", "estado=1", "", $pagina);
        return $data;
    }

    function listaMarcaPaginadoxNombre($pagina, $condicion) {
        $condicion = htmlentities($condicion, ENT_QUOTES, 'UTF-8');
        $data = $this->leeRegistroPaginado(
                $this->tabla, "", "nombre like '%$condicion%' and estado=1  ", "", $pagina);
        return $data;
    }

    function grabaMarca($data) {
        $id = $this->grabaRegistro($this->tabla, $data);
        return $id;
    }

    function actualizaMarca($data, $id) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idmarca=$id");
        return $exito;
    }

    function eliminaMarca($id) {
        $exito = $this->cambiaEstado($this->tabla, "idmarca=$id");
        return $exito;
    }

    function paginadoMarca() {
        return $this->paginado($this->tabla, "estado=1");
    }

    function paginadoMarcaxNombre($condicion) {
        $condicion = htmlentities($condicion, ENT_QUOTES, 'UTF-8');
        return $this->paginado($this->tabla, "nombre like '%$condicion%'  and estado=1");
    }

}

?>